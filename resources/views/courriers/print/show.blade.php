<!doctype html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $courrier->numero }}</title>
    <style>
        @page { size: A4 portrait; margin: 16mm 15mm; }
        * { box-sizing: border-box; }
        body { color: #17213c; font-family: Arial, sans-serif; font-size: 11px; line-height: 1.45; margin: 0; }
        .document { max-width: 760px; margin: 0 auto; }
        .toolbar { display: flex; justify-content: flex-end; margin-bottom: 16px; }
        .toolbar button { background: #0b3d91; border: 0; border-radius: 6px; color: #fff; cursor: pointer; font: inherit; font-weight: 700; padding: 9px 14px; }
        .header { align-items: center; border-bottom: 2px solid #b78b29; display: flex; gap: 14px; margin-bottom: 18px; padding-bottom: 11px; }
        .logo { height: 62px; object-fit: contain; width: 62px; }
        h1 { color: #0b3d91; font-size: 20px; margin: 0; }
        .meta { color: #4d5870; margin: 4px 0 0; }
        .badges { margin: 15px 0 18px; }
        .badge { border: 1px solid #cbd5e1; border-radius: 999px; display: inline-block; font-size: 10px; margin-right: 6px; padding: 4px 8px; }
        .details { border-top: 1px solid #dce2ec; display: grid; grid-template-columns: 1fr 1fr; margin-top: 12px; }
        .field { border-bottom: 1px solid #dce2ec; min-height: 67px; padding: 12px 14px 12px 0; }
        .field:nth-child(even) { padding-left: 14px; padding-right: 0; }
        .field-wide { grid-column: 1 / -1; padding-right: 0; }
        dt { color: #64748b; font-size: 10px; font-weight: 700; margin-bottom: 4px; text-transform: uppercase; }
        dd { margin: 0; white-space: pre-line; }
        .footer { color: #6b7280; font-size: 8px; margin-top: 16px; text-align: right; }
        @media print { .toolbar { display: none; } }
    </style>
</head>
<body>
    <main class="document">
        <div class="toolbar"><button type="button" onclick="window.print()">Imprimer</button></div>

        <header class="header">
            <img src="{{ asset('assets/commune-jerada.jpg') }}" alt="Logo de la Commune" class="logo">
            <div>
                <h1>{{ $courrier->numero }}</h1>
                <p class="meta">Fiche du courrier - g&eacute;n&eacute;r&eacute;e le {{ $generatedAt->locale(app()->getLocale())->translatedFormat('d F Y') }} &agrave; {{ $generatedAt->format('H:i') }}</p>
            </div>
        </header>

        <h2 style="font-size: 16px; margin: 0;">{{ $courrier->objet }}</h2>
        <div class="badges"><span class="badge">{{ $courrier->statusLabel() }}</span><span class="badge">{{ $courrier->priorityLabel() }}</span></div>

        <dl class="details">
            <div class="field"><dt>Exp&eacute;diteur</dt><dd>{{ $courrier->expediteur }}</dd></div>
            <div class="field"><dt>Service</dt><dd>{{ $courrier->service?->name ?? '-' }}</dd></div>
            <div class="field"><dt>Date de r&eacute;ception</dt><dd>{{ $courrier->date_reception?->format('d/m/Y') ?? '-' }}</dd></div>
            <div class="field"><dt>R&eacute;f&eacute;rence</dt><dd>{{ $courrier->reference ?: '-' }}</dd></div>
            <div class="field"><dt>Type</dt><dd>{{ $courrier->type }}</dd></div>
            <div class="field"><dt>Enregistr&eacute; par</dt><dd>{{ $courrier->creator?->name ?? '-' }}</dd></div>

            @if ($courrier->traite_le)
                <div class="field"><dt>Date de traitement</dt><dd>{{ $courrier->traite_le->format('d/m/Y H:i') }}</dd></div>
            @endif
            @if ($courrier->archived_at)
                <div class="field"><dt>Date d'archivage</dt><dd>{{ $courrier->archived_at->format('d/m/Y H:i') }}</dd></div>
            @endif
            @if ($courrier->description)
                <div class="field field-wide"><dt>Description</dt><dd>{{ $courrier->description }}</dd></div>
            @endif
            @if ($courrier->traitement_commentaire)
                <div class="field field-wide"><dt>Commentaire de traitement</dt><dd>{{ $courrier->traitement_commentaire }}</dd></div>
            @endif
        </dl>

        <p class="footer">SGMC - Syst&egrave;me de Gestion des Courriers</p>
    </main>
</body>
</html>