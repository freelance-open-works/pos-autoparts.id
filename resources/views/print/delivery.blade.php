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
            <td style="width: 150px;">
                <img src="{{ $setting->getStoragePath('app_logo') }}" style="width: 150px;" />
            </td>
            <td class="text-center pr-80">
                <span class="font-bold text-2xl">SURAT JALAN</span>
                <pre class="font-sans text-xs">{{ $setting->getValueByKey('company_address') }}</pre>
            </td>
        </tr>

    </table>

    <table class="text-xs" style="width: 100%;table-layout: fixed;word-wrap: break-word;">
        <tbody class="">
            <tr class="border border-black" style="border: 1px solid black;">
                <td class="border border-black" style="border: 1px solid black;vertical-align: top;" rowspan="4">
                    <span class="font-bold">Tanggal</span> : {{ formatDate($delivery->sd_date) }} <br />
                    <span class="font-bold">No Surat Jalan</span> : {{ $delivery->sd_code }} <br />
                    <span class="font-bold">Kode Customer</span> : {{ $sale->customer->code }} <br /><br />
                    <span class="font-bold">PENGIRIM</span><br />
                    <div class="font-sans" style="overflow-wrap: break-word;">
                        Nama : {{ $setting->getValueByKey('company_name') }}<br />
                        Alamat : {{ $setting->getValueByKey('company_address') }}
                    </div>
                </td>
                <td class="border border-black" style="border: 1px solid black;">
                    <span class="font-bold">ASAL</span> : MAKASSAR <br />
                    <span class="font-bold">TUJUAN</span> : {{ $sale->address }} <br />
                    <span class="font-bold">LAYANAN</span> : {{ $delivery->service }} <br />
                </td>
            </tr>
            <tr>
                <td class="border border-black font-bold text-center" style="border: 1px solid black;">DOKUMEN NO.</td>
            </tr>
            <tr>
                <td class="border border-black font-bold text-center" style="border: 1px solid black;">{{ $sale->s_code }}</td>
            </tr>
            <tr>
                <td class="border border-black text-center" style="height: 80px;border: 1px solid black;">
                    <pre class="font-sans text-center">{{ $delivery->note_manual }}</pre>
                </td>
            </tr>
            <tr class="border border-black">
                <td class="border border-black" style="border: 1px solid black;vertical-align: top;">
                    <span class="font-bold">PENERIMA</span>
                    <pre class="font-sans">Nama : {{ $sale->customer->name }}
Alamat : {{ $sale->address }}
                    </pre>
                    <span class="font-bold">DIANGKUT OLEH</span>
                    <pre class="font-sans">Ekspedisi : {{ $delivery->expedition->name }}
Alamat : {{ $delivery->expedition->address }}</pre>
                </td>
                <td class="border border-black" style="border: 1px solid black;vertical-align: top;">
                    <table style="width: 101%;table-layout: fixed;vertical-align: top;margin: -1px;">
                        <tr>
                            <td colspan="2"><span class="text-center w-full font-bold">TOTAL BARANG</span></td>
                        </tr>
                        <tr>
                            <td class="text-center py-4" style="border: 1px solid black;">
                                QTY ({{ $delivery->qty_unit }})
                            </td>
                            <td class="text-center py-4" style="border: 1px solid black;">BERAT VOLUME ({{ $delivery->volume_unit }})</td>
                        </tr>
                        <tr>
                            <td class="text-center py-4" style="border: 1px solid black;">
                                {{ $delivery->qty }}
                            </td>
                            <td class="text-center py-4" style="border: 1px solid black;">{{ $delivery->volume }} </td>
                        </tr>
                    </table>
                </td>
            </tr>
            <tr class="border border-black">
                <td class="border border-black" style="vertical-align: top;border: 1px solid black;height: 80px;">
                    <span class="font-bold">DISCLAIMER</span>
                    <div class="font-sans" style="font-size: 0.68rem;">
                        1) BUKTI BARANG DI TERIMA DALAM KEADAAN BAIK DAN CUKUP<br />
                        2) LEMBAR PUTIH KEMBALI<br />
                        3) TANDA TANGAN DAN STEMPEL<br />
                    </div>
                </td>
                <td class="border border-black" style="vertical-align: top;border: 1px solid black;">
                    <span class="font-bold">KETERANGAN : </span>
                    <pre class="font-sans">{{ $delivery->note }}</pre>
                </td>
            </tr>
        </tbody>
    </table>
    <table class="w-full pt-4 text-center mt-4">
        <tbody>
            <tr>
                <td style="width: 25%;">Admin</td>
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
    <div class="border border-collapse border-black " style="border: 1px solid black;">
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