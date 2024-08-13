<!DOCTYPE html>
<html lang="en" data-theme="winter">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <x-print-shared />
    <style>
        .page-break {
            page-break-after: always;
        }
    </style>
</head>

<body>
    <table class="w-full mb-4">
        <tr>
            <td rowspan="2" style="width: 200px;">
                <img src="{{ $setting->getStoragePath('app_logo') }}" style="width: 200px;" />
            </td>
            <td class="text-center pr-48">
                <span class="font-bold text-2xl">SURAT JALAN</span>
            </td>
        </tr>
        <tr>
            <td class="text-center">
                <pre class="font-sans text-xs">{{ $setting->getValueByKey('company_address') }}</pre>
            </td>
        </tr>
    </table>
    <hr />
    <table class="w-full table table-zebra border-collapse">
        <tbody>
            <tr class="border border-black">
                <td class="border border-black" style="width: 50%;">
                    <span class="font-bold">Tanggal</span> : {{ formatDateString($delivery->sd_date) }}
                </td>
                <td class="border border-black" colspan="2">
                    <span class="font-bold">Layanan</span> : {{ $delivery->service }}
                </td>
            </tr>
            <tr class="border border-black">
                <td class="border border-black">
                    <span class="font-bold">No Surat Jalan</span> : {{ $delivery->sd_code }}
                </td>
                <td class="border border-black font-bold text-center" colspan="2">DOKUMEN NO.</td>
            </tr>
            <tr class="border border-black">
                <td class="border border-black"></td>
                <td class="border border-black font-bold text-center" colspan="2">{{ $sale->s_code }}</td>
            </tr>
            <tr class="border border-black">
                <td class="border border-black font-bold">PENGIRIM</td>
                <td class="border border-black font-bold" colspan="2">TOTAL BARANG</td>
            </tr>
            <tr class="border border-black">
                <td class="border border-black">Nama : {{ $setting->getValueByKey('company_name') }}</td>
                <td class="border border-black text-center" rowspan="2">QTY ({{ $delivery->qty_unit }})</td>
                <td class="border border-black text-center" rowspan="2">BERAT VOLUME ({{ $delivery->volume_unit }})</td>
            </tr>
            <tr class="border border-black">
                <td class="border border-black">Alamat : {{ $setting->getValueByKey('company_address') }}</td>
            </tr>
            <tr class="border border-black">
                <td class="border border-black font-bold">PENERIMA</td>
                <td class="border border-black text-center" rowspan="3"> {{ $delivery->qty }} </td>
                <td class="border border-black text-center" rowspan="3"> {{ $delivery->volume }} </td>
            </tr>
            <tr class="border border-black">
                <td class="border border-black">Nama : {{ $sale->customer->name }}</td>
            </tr>
            <tr class="border border-black">
                <td class="border border-black">Alamat : {{$sale->address }}</td>
            </tr>
            <tr class="border border-black">
                <td class="border border-black font-bold">DIANGKUT OLEH</td>
                <td class="border border-black font-bold" colspan="2">KETERANGAN : </td>
            </tr>
            <tr class="border border-black">
                <td class="border border-black">Ekspedisi : {{ $delivery->expedition->name }}</td>
                <td class="border border-black" colspan="2" rowspan="3">
                    <pre class="font-sans">{{ $delivery->note }}</pre>
                </td>
            </tr>
            <tr class="border border-black">
                <td class="border border-black">Alamat : {{ $delivery->expedition->address }}</td>
            </tr>
            <tr class="border border-black">
                <td class="border border-black">
                    <pre class="font-sans"><span class="font-bold">DISCLAIMER</span>
1) BUKTI BARANG DI TERIMA DALAM KEADAAN BAIK DAN CUKUP
2) LEMBAR PUTIH DAN MERAH KEMBALI
3) TANDA TANGAN DAN STEMPEL
                    </pre>
                </td>
            </tr>
        </tbody>
    </table>
    <table class="w-full pt-4 text-center mt-4">
        <tbody>
            <tr>
                <td style="width: 25%;"></td>
                <td style="width: 25%;"></td>
                <td class="text-center font-bold">EKSPEDISI</td>
            </tr>
            <tr>
                <td style="width: 25%;"></td>
                <td style="width: 25%;"></td>
                <td class="text-center font-bold">{{ $delivery->expedition->name }}</td>
            </tr>
            <tr>
                <td style="width: 25%;"></td>
                <td style="width: 25%;"></td>
                <td class="text-center pt-10 italic">Tanda Tangan + Stempel</td>
            </tr>
        </tbody>
    </table>
    <div class="page-break"></div>
    <div class="border border-collapse border-black ">
        <table class="" style="max-width: 200px;">
            <tr class="">
                <td class="px-4 text-center" style="width: 200px;"><img src="{{ $setting->getStoragePath('app_logo') }}" width="200px" />
                </td>
                <td class="font-bold">
                    <pre class="font-sans">Pengirim: 
<span class="font-bold text-lg">{{ $setting->getValueByKey('company_name') }}</span>
<pre class="font-sans text-xs">{{ $setting->getValueByKey('company_address') }}</pre>
                    </pre>
                </td>
            </tr>
            <tr>
                <td class="text-center p-4">
                    <img src="{{ storage_path('/app/default/frigle1.png') }}" width="150px" class="mb-2" />
                    <img src="{{ storage_path('/app/default/frigle2.png') }}" width="150px" class="mb-2" />
                </td>
                <td>
                    <pre class="font-sans text-2xl">Penerima
<span class="text-lg">{{ $sale->customer->name }}<span>
<span class="mb-2 font-bo text-sm">{{ $sale->address }}</span>

Nomor Invoice
<span class="text-lg mb-2 w-full">{{ $sale->s_code }}<span>

Nomor Surat Jalan
<span class="text-lg mb-2">{{ $delivery->sd_code }}<span></pre>
                </td>
            </tr>
        </table>
    </div>
</body>

</html>