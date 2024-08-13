<!DOCTYPE html>
<html lang="en" data-theme="winter">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <x-print-shared />
</head>

<body>
    <table class="w-full mb-4">
        <tr>
            <td>
                <img src="{{ $setting->getStoragePath('app_logo') }}" style="width: 200px;" />
            </td>
            <td class="font-bold text-2xl text-right">Nota Pembelian</td>
        </tr>
        <tr>
            <td>
                <pre class="font-sans text-xs">{{ $setting->getValueByKey('company_address') }}</pre>
            </td>
            <td></td>
        </tr>
    </table>
    <hr />
    <table class="mb-4">
        <tr>
            <td class="font-bold capitalize">NO PO</td>
            <td class="px-2">:</td>
            <td> {{ $purchase->purchaseOrder->po_code }}</td>
        </tr>
        <tr>
            <td class="font-bold capitalize">Tanggal PO</td>
            <td class="px-2">:</td>
            <td> {{ formatDate($purchase->p_date) }}</td>
        </tr>
        <tr>
            <td class="font-bold capitalize">Nama Supplier</td>
            <td class="px-2">:</td>
            <td> {{ $purchase->supplier->name }}</td>
        </tr>
        <tr>
            <td class="font-bold capitalize">Alamat Supplier</td>
            <td class="px-2">:</td>
            <td> {{ $purchase->address }}</td>
        </tr>
    </table>
    <table class="border-collapse border border-black w-full">
        <thead>
            <tr class="border border-black">
                <td class="border border-black font-bold p-1 max-w-sm">
                    No
                </td>
                <td class="border border-black font-bold p-1">
                    Part No
                </td>
                <td class="border border-black font-bold p-1">
                    Part Name
                </td>
                <td class="border border-black font-bold p-1">
                    Merek
                </td>
                <td class="border border-black font-bold p-1 text-right">
                    Qty
                </td>
                <td class="border border-black font-bold p-1 text-right">
                    Harga
                </td>
                <td class="border border-black font-bold p-1 text-right">
                    Diskon 1 (%)
                </td>
                <td class="border border-black font-bold p-1 text-right">
                    Diskon 2 (%)
                </td>
                <td class="border border-black font-bold p-1 text-right">
                    Amount Diskon
                </td>
                <td class="border border-black font-bold p-1 text-right">
                    Subtotal
                </td>
            </tr>
        </thead>
        <tbody>
            @foreach ($items as $index => $item)
            <tr class="border border-black">
                <td class="border border-black p-1 max-w-sm">
                    {{ $index + 1 }}
                </td>
                <td class="border border-black p-1">
                    {{ $item->product->part_code }}
                </td>
                <td class="border border-black p-1">
                    {{ $item->product->name }}
                </td>
                <td class="border border-black p-1">
                    {{ $item->product->brand->name }}
                </td>
                <td class="border border-black p-1 text-right">
                    {{ formatIDR($item->qty) }}
                </td>
                <td class="border border-black p-1 text-right">
                    {{ formatIDR($item->cost) }}
                </td>
                <td class="border border-black p-1 text-right">
                    {{ formatIDR($item->discount_percent_2) }}
                </td>
                <td class="border border-black p-1 text-right">
                    {{ formatIDR($item->discount_percent_1) }}
                </td>
                <td class="border border-black p-1 text-right">
                    {{ formatIDR($item->discount_total) }}
                </td>
                <td class="border border-black p-1 text-right">
                    {{ formatIDR($item->subtotal_discount) }}
                </td>
            </tr>
            @endforeach
            @foreach ([
            'Harga Beli' => $purchase->amount_cost ,
            'Diskon' => $purchase->amount_discount,
            'Grand Total' => $purchase->amount_cost - $purchase->amount_discount,
            'DPP' => $purchase->amount_net,
            'PPN' => $purchase->amount_ppn,
            ] as $key => $value)
            <tr class="border border-black">
                <td class="border border-black p-1 max-w-sm" colspan="7">
                </td>
                <td class="border border-black p-1 text-right font-bold" colspan="2">
                    {{ $key }}
                </td>
                <td class="border border-black p-1 text-right">
                    {{ formatIDR($value) }}
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    <table class="w-full pt-4 text-center">
        <tbody>
            <tr>
                <td class="text-left text-sm">Keterangan : </td>
            </tr>
            <tr>
                <td class="text-left" style="width: 200px;">
                    <pre class="font-sans text-sm" style="width: 200px;">{{ $purchase->note }}</pre>
                </td>
            </tr>
        </tbody>
    </table>
    <table class="w-full pt-4 text-center mt-4">
        <tbody>
            <tr>
                <td class="text-left ">
                    <pre class="font-sans text-xs">- Semua pengiriman barang harus disertakan Nota/Faktur
- Barang akan kami kembalikan jika tidak sesuai PO
                </pre>
                </td>
                <td class="text-center font-bold"></td>
            </tr>
            <tr>
                <td class="text-left text-xs"> </td>
                <td class="text-center font-bold">Autopart Sales Indonesia</td>
            </tr>
            <tr>
                <td class=""></td>
                <td class="pt-10">{{ $purchase->creator->name }}</td>
            </tr>
        </tbody>
    </table>
</body>

</html>