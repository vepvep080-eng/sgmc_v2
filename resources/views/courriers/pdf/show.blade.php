<!doctype html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <title>Fiche du courrier</title>
    <style>
        @page { margin: 14mm 13mm; }
        * { box-sizing: border-box; }
        body { color: #17213c; font-family: DejaVu Sans, sans-serif; font-size: 9px; line-height: 1.4; }
        .header { border-bottom: 2px solid #b78b29; margin-bottom: 16px; padding-bottom: 10px; width: 100%; }
        .logo { height: 56px; object-fit: contain; width: 56px; }
        .heading { display: inline-block; margin-left: 12px; vertical-align: middle; }
        h1 { color: #0b3d91; font-size: 18px; margin: 0 0 3px; }
        h2 { color: #0b3d91; font-size: 13px; margin: 18px 0 8px; }
        .meta { color: #4d5870; margin: 0; }
        .badges { margin: 10px 0 13px; }
        .badge { border: 1px solid #cbd5e1; border-radius: 10px; display: inline-block; margin-right: 5px; padding: 4px 7px; }
        .details { border-collapse: collapse; table-layout: fixed; width: 100%; }
        .details td { border-bottom: 1px solid #dce2ec; padding: 8px 10px 8px 0; vertical-align: top; width: 50%; }
        .details td.right { padding-left: 10px; padding-right: 0; }
        .label { color: #64748b; font-size: 8px; font-weight: bold; margin-bottom: 3px; text-transform: uppercase; }
        .value { white-space: pre-line; word-wrap: break-word; }
        .history { border-collapse: collapse; table-layout: fixed; width: 100%; }
        .history th { background: #0b3d91; color: #fff; font-size: 8px; padding: 6px 5px; text-align: left; }
        .history td { border-bottom: 1px solid #dce2ec; padding: 6px 5px; vertical-align: top; word-wrap: break-word; }
        .footer { color: #6b7280; font-size: 8px; margin-top: 14px; text-align: right; }
    </style>
</head>
<body>
    <table class="header">
        <tr>
            <td style="border: 0; padding: 0; width: 68px;">
                @if ($logo)<img src="{{ $logo }}" alt="Logo de la Commune" class="logo">@endif
            </td>
            <td style="border: 0; padding: 0;">
                <div class="heading">
                    <h1>Fiche du courrier</h1>
                    <p class="meta">G&eacute;n&eacute;r&eacute;e le {{ $generatedAt->locale('fr')->translatedFormat('d F Y') }} &agrave; {{ $generatedAt->format('H:i') }}</p>
                </div>
            </td>
        </tr>
    </table>

    <h2>{{ $courrier->numero }} - {{ $arabic->shape($courrier->objet) }}</h2>
    <div class="badges"><span class="badge">{{ $courrier->statusLabel() }}</span><span class="badge">{{ $courrier->priorityLabel() }}</span></div>

    <table class="details">
        <tr>
            <td><div class="label">Exp&eacute;diteur</div><div class="value">{{ $arabic->shape($courrier->expediteur) }}</div></td>
            <td class="right"><div class="label">Service concern&eacute;</div><div class="value">{{ $arabic->shape($courrier->service?->name ?? '-') }}</div></td>
        </tr>
        <tr>
            <td><div class="label">Date de r&eacute;ception</div><div class="value">{{ $courrier->date_reception?->format('d/m/Y') ?? '-' }}</div></td>
            <td class="right"><div class="label">R&eacute;f&eacute;rence</div><div class="value">{{ $arabic->shape($courrier->reference ?: '-') }}</div></td>
        </tr>
        <tr>
            <td><div class="label">Type</div><div class="value">{{ $arabic->shape($courrier->type) }}</div></td>
            <td class="right"><div class="label">Enregistr&eacute; par</div><div class="value">{{ $arabic->shape($courrier->creator?->name ?? '-') }}</div></td>
        </tr>
        @if ($courrier->traite_le || $courrier->archived_at)
            <tr>
                <td>
                    @if ($courrier->traite_le)<div class="label">Date de traitement</div><div class="value">{{ $courrier->traite_le->format('d/m/Y H:i') }}</div>@endif
                </td>
                <td class="right">
                    @if ($courrier->archived_at)<div class="label">Date d'archivage</div><div class="value">{{ $courrier->archived_at->format('d/m/Y H:i') }}</div>@endif
                </td>
            </tr>
        @endif
        @if ($courrier->description)
            <tr><td colspan="2" style="padding-right: 0;"><div class="label">Description</div><div class="value">{{ $arabic->shape($courrier->description) }}</div></td></tr>
        @endif
        @if ($courrier->traitement_commentaire)
            <tr><td colspan="2" style="padding-right: 0;"><div class="label">Commentaire de traitement</div><div class="value">{{ $arabic->shape($courrier->traitement_commentaire) }}</div></td></tr>
        @endif
    </table>

    @if ($courrier->routings->isNotEmpty())
        <h2>Historique de routage</h2>
        <table class="history">
            <thead>
                <tr><th>Source</th><th>Destination</th><th>Utilisateur</th><th>Statut</th><th>Date</th><th>Commentaire</th></tr>
            </thead>
            <tbody>
                @foreach ($courrier->routings as $routing)
                    <tr>
                        <td>{{ $arabic->shape($routing->serviceSource?->name ?? "Bureau d'Ordre") }}</td>
                        <td>{{ $arabic->shape($routing->serviceDestination?->name ?? '-') }}</td>
                        <td>{{ $arabic->shape($routing->routedBy?->name ?? '-') }}</td>
                        <td>{{ $routing->statusLabel() }}</td>
                        <td>{{ $routing->sent_at?->format('d/m/Y H:i') ?? '-' }}</td>
                        <td>{{ $arabic->shape($routing->commentaire ?: '-') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    <p class="footer">SGMC - Syst&egrave;me de Gestion des Courriers</p>
</body>
</html>