# SGMC Windows Scanner Agent

Local Windows component that bridges physical scanners to the SGMC Laravel application.

## Architecture

```
[Physical Scanner]
       ↓
[Windows Scanner Agent]  ← this component
       ↓
[localhost HTTP + one-time token]
       ↓
[SGMC Laravel Application]
       ↓
[Selected Courrier] → [Protected attachment]
```

## Security Model

- **Localhost-only**: binds to `127.0.0.1`, never exposed publicly
- **No persistent SGMC credentials**: agent only receives short-lived one-time tokens
- **Short-lived jobs**: tokens expire in 15 minutes (matching SGMC `scan_jobs.expires_at`)
- **One-time use**: tokens are hashed in SGMC and cannot be reused
- **No scanner-brand logic in SGMC**: all provider-specific code lives here

## Communication Protocol

### 1. Create Scan Job (SGMC side)

The SGMC web application creates a `ScanJob` with:
- UUID `public_id`
- Hashed one-time token
- 15-minute expiry
- Target courrier ID

### 2. Agent Receives Job

The browser sends the job payload to the agent at `http://127.0.0.1:8765/scan`:

```json
{
  "job_id": "uuid-here",
  "token": "64-char-random-token",
  "courrier_id": 123,
  "expires_at": "2026-08-17T12:00:00+01:00"
}
```

### 3. Agent Scans

1. Agent validates the token (one-time, not expired)
2. Agent discovers available scanners via providers
3. User selects a scanner (or default is used)
4. Agent scans the physical document
5. Agent saves to a temporary local file

### 4. Agent Uploads Result

Agent uploads the scanned file to SGMC:

```
POST /scan-jobs/{scanJob}/upload
Content-Type: multipart/form-data

token: <one-time-token>
document: <file>
```

SGMC validates:
- Token (hashed, one-time, not expired)
- File type (pdf, doc, docx, jpg, jpeg, png)
- File size (max 10MB)
- User authorization (update permission on courrier)

### 5. Job Completes

SGMC:
- Attaches the document to the courrier
- Marks the job as `completed`
- Records audit events
- Returns success to the agent

## Provider Abstraction

```php
interface ScannerProviderInterface
{
    public function discover(): array;  // list available scanners
    public function scan(string $scannerId, array $options = []): string;  // returns file path
    public function isAvailable(): bool;
    public function name(): string;
}
```

### Providers

| Provider | Status | Notes |
|---|---|---|
| `WiaProvider` | **Requires Windows validation** | Uses Windows Image Acquisition (WIA) |
| `TwainProvider` | **Requires Windows validation** | Uses TWAIN standard |
| `NetworkProvider` | **Requires Windows validation** | For network-connected scanners |

**IMPORTANT**: Hardware providers are NOT implemented as working drivers in this repository. They are contract stubs that require Windows-side validation with actual scanner hardware. The provider interface and discovery architecture are complete, but actual scanning requires a Windows machine with compatible drivers.

## Setup

### Requirements

- Windows 10/11
- PHP 8.3+ (for the agent server)
- Scanner with WIA or TWAIN drivers installed

### Running

```bash
cd scanner-agent
php bin/agent.php start
```

The agent will start on `http://127.0.0.1:8765`.

### Configuration

Edit `config/agent.json`:

```json
{
  "host": "127.0.0.1",
  "port": 8765,
  "sgmc_url": "http://localhost:8000",
  "temp_dir": "storage/temp",
  "max_file_size": 10485760,
  "allowed_mimes": ["pdf", "doc", "docx", "jpg", "jpeg", "png"]
}
```

## Testing

```bash
cd scanner-agent
php tests/run.php
```

**Note**: Provider contract tests use mocks. Real hardware validation requires a Windows machine with a compatible scanner.

## Status

- ✅ Architecture implemented
- ✅ Provider abstraction contract
- ✅ Security model (one-time tokens, localhost-only)
- ✅ Communication protocol defined
- ⚠️ WIA/TWAIN/Network providers: **contract stubs only** — require Windows validation
- ⚠️ Real hardware scanning: **NOT tested** — requires Windows machine with scanner
