<!doctype html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Liste des courriers</title>
    <style>
        @page { size: A4 landscape; margin: 12mm 10mm; }
        * { box-sizing: border-box; }
        body { color: #17213c; font-family: Arial, sans-serif; font-size: 10px; line-height: 1.35; margin: 0; }
        .document { max-width: 1120px; margin: 0 auto; }
        .toolbar { display: flex; justify-content: flex-end; margin-bottom: 16px; }
        .toolbar button { background: #0b3d91; border: 0; border-radius: 6px; color: #fff; cursor: pointer; font: inherit; font-weight: 700; padding: 9px 14px; }
        .header { align-items: center; border-bottom: 2px solid #b78b29; display: flex; gap: 14px; margin-bottom: 16px; padding-bottom: 10px; }
        .logo { height: 58px; object-fit: contain; width: 58px; }
        h1 { color: #0b3d91; font-size: 20px; margin: 0 0 4px; }
        .meta { color: #4d5870; margin: 0; }
        table { border-collapse: collapse; table-layout: fixed; width: 100%; }
        th { background: #0b3d91; color: #fff; font-size: 9px; padding: 8px 6px; text-align: left; }
        td { border-bottom: 1px solid #dce2ec; padding: 7px 6px; vertical-align: top; word-wrap: break-word; }
        tr:nth-child(even) td { background: #f5f7fb; }
        .numero { width: 13%; }
        .objet { width: 23%; }
        .expediteur { width: 17%; }
        .service { width: 16%; }
        .date { width: 11%; }
        .priorite { width: 10%; }
        .statut { width: 10%; }
        .footer { color: #6b7280; font-size: 8px; margin-top: 12px; text-align: right; }
        @media print { .toolbar { display: none; } }
    </style>
</head>
<body>
    <main class="document">
        <div class="toolbar"><button type="button" onclick="window.print()">Imprimer</button></div>

        <header class="header">
            <img src="{{ asset('assets/commune-jerada.jpg') }}" alt="Logo de la Commune" class="logo">
            <div>
                <h1>Liste des courriers</h1>
                <p class="meta">G&eacute;n&eacute;r&eacute; le {{ $generatedAt->locale('fr')->translatedFormat('d F Y') }} &agrave; {{ $generatedAt->format('H:i') }}</p>
            </div>
        </header>

        <table>
            <thead>
                <tr>
                    <th class="numero">Num&eacute;ro</th>
                    <th class="objet">Objet</th>
                    <th class="expediteur">Exp&eacute;diteur</th>
                    <th class="service">Service</th>
                    <th class="date">Date de r&eacute;ception</th>
                    <th class="priorite">Priorit&eacute;</th>
                    <th class="statut">Statut</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($courriers as $courrier)
                    <tr>
                        <td>{{ $courrier->numero }}</td>
                        <td>{{ $courrier->objet }}</td>
                        <td>{{ $courrier->expediteur }}</td>
                        <td>{{ $courrier->service?->name ?? '-' }}</td>
                        <td>{{ $courrier->date_reception?->format('d/m/Y') ?? '-' }}</td>
                        <td>{{ $courrier->priorityLabel() }}</td>
                        <td>{{ $courrier->statusLabel() }}</td>
                    </tr>
                @empty
                    <tr><td colspan="7" style="text-align: center; padding: 18px;">Aucun courrier ne correspond aux crit&egrave;res s&eacute;lectionn&eacute;s.</td></tr>
                @endforelse
            </tbody>
        </table>

        <p class="footer">SGMC - Syst&egrave;me de Gestion des Courriers</p>
    </main>
</body>
</html>