<!DOCTYPE html>
<html>
<head>
    <title>Print Kategori Item</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        table, th, td {
            border: 1px solid black;
        }

        th, td {
            padding: 6px;
            text-align: left;
        }

        .footer {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            text-align: right;
            font-size: 10px;
        }
    </style>
</head>
<body>

<div class="header">
    <h3>LAPORAN KATEGORI ITEM</h3>
</div>

<p><b>Nama Kategori:</b> {{ $kategori->nama }}</p>
<p><b>Kode Kategori:</b> {{ $kategori->kode }}</p>

<br>

<table>
    <thead>
        <tr>
            <th>No</th>
            <th>Kode Item</th>
            <th>Nama Item</th>
            <th>Harga Beli</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($kategori->masterItems as $i => $item)
            <tr>
                <td>{{ $i + 1 }}</td>
                <td>{{ $item->kode }}</td>
                <td>{{ $item->nama }}</td>
                <td>{{ $item->harga_beli }}</td>
            </tr>
        @endforeach
    </tbody>
</table>

<div class="footer">
    Dicetak pada: {{ $tanggal->format('d-m-Y H:i:s') }}
</div>

</body>
</html>
