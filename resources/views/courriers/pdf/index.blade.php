<!doctype html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <title>Liste des courriers</title>
    <style>
        @page { margin: 12mm 10mm; }
        * { box-sizing: border-box; }
        body { color: #17213c; font-family: DejaVu Sans, sans-serif; font-size: 9px; line-height: 1.35; }
        .header { width: 100%; border-bottom: 1px solid #b78b29; padding-bottom: 10px; margin-bottom: 14px; }
        .logo { width: 54px; height: 54px; object-fit: contain; vertical-align: middle; }
        .heading { display: inline-block; margin-left: 12px; vertical-align: middle; }
        h1 { color: #0b3d91; font-size: 18px; font-weight: 700; margin: 0 0 4px; }
        .meta { color: #4d5870; font-size: 9px; margin: 2px 0; }
        table { border-collapse: collapse; table-layout: fixed; width: 100%; }
        th { background: #0b3d91; color: #fff; font-size: 8px; font-weight: 700; padding: 7px 5px; text-align: left; text-transform: uppercase; }
        td { border-bottom: 1px solid #dce2ec; padding: 6px 5px; vertical-align: top; word-wrap: break-word; }
        tr:nth-child(even) td { background: #f5f7fb; }
        .numero { width: 13%; }
        .objet { width: 23%; }
        .expediteur { width: 17%; }
        .service { width: 16%; }
        .date { width: 11%; }
        .priorite { width: 10%; }
        .statut { width: 10%; }
        .footer { color: #6b7280; font-size: 8px; margin-top: 12px; text-align: right; }
    </style>
</head>
<body>
    <table class="header">
        <tr>
            <td style="border: 0; padding: 0; width: 66px; background: transparent;">
                @if ($logo)
                    <img src="{{ $logo }}" alt="Logo de la Commune" class="logo">
                @endif
            </td>
            <td style="border: 0; padding: 0; background: transparent;">
                <div class="heading">
                    <h1>Liste des courriers</h1>
                    <p class="meta">Généré le {{ $generatedAt->locale(app()->getLocale())->translatedFormat('d F Y à H:i') }}</p>
                    <p class="meta">Par : {{ $generatedBy->name }}</p>
                </div>
            </td>
        </tr>
    </table>

    <table>
        <thead>
            <tr>
                <th class="numero">Numéro</th>
                <th class="objet">Objet</th>
                <th class="expediteur">Expéditeur</th>
                <th class="service">Service</th>
                <th class="date">Date de réception</th>
                <th class="priorite">Priorité</th>
                <th class="statut">Statut</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($courriers as $courrier)
                <tr>
                    <td>{{ $courrier->numero }}</td>
                    <td>{{ $courrier->objet }}</td>
                    <td>{{ $courrier->expediteur }}</td>
                    <td>{{ $courrier->service?->name ?? '—' }}</td>
                    <td>{{ $courrier->date_reception?->locale(app()->getLocale())->translatedFormat('d/m/Y') ?? '—' }}</td>
                    <td>{{ $courrier->priorityLabel() }}</td>
                    <td>{{ $courrier->statusLabel() }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" style="text-align: center; padding: 16px;">Aucun courrier ne correspond aux critères sélectionnés.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <p class="footer">SGMC — Système de Gestion des Courriers</p>
</body>
</html>
