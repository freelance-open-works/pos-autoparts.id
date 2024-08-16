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
            <td class="font-bold text-2xl text-right">SALES INVOICE</td>
        </tr>
        <tr>
            <td>
                <pre class="font-sans text-xs">{{ $setting->getValueByKey('company_address') }}</pre>
            </td>
            <td></td>
        </tr>
    </table>
    <hr />
    <table class="w-full mb-4">
        <tr>
            <td class="font-bold capitalize">To</td>
            <td class="px-2">:</td>
            <td> {{ $sale->customer->name }}</td>
            <td class="font-bold capitalize">Invoice Number</td>
            <td class="px-2">:</td>
            <td> {{ $sale->s_code }}</td>
        </tr>
        <tr>
            <td class="font-bold capitalize">Address</td>
            <td class="px-2">:</td>
            <td> {{ $sale->address }}</td>
            <td class="font-bold capitalize">Invoice Date</td>
            <td class="px-2">:</td>
            <td> {{ formatDate($sale->s_date) }}</td>
        </tr>
    </table>
    <table class="border-collapse border border-black w-full text-xs">
        <thead>
            <tr class="border border-black">
                <td class="border border-black font-bold p-1 max-w-sm" style="border: 1px solid black;">
                    No
                </td>
                <td class="border border-black font-bold p-1" style="border: 1px solid black;">
                    Part No
                </td>
                <td class="border border-black font-bold p-1" style="border: 1px solid black;">
                    Part Name
                </td>
                <td class="border border-black font-bold p-1" style="border: 1px solid black;">
                    Merek
                </td>
                <td class="border border-black font-bold p-1 text-right" style="border: 1px solid black;">
                    Qty
                </td>
                <td class="border border-black font-bold p-1 text-right" style="border: 1px solid black;">
                    Harga
                </td>
                <td class="border border-black font-bold p-1 text-right" style="border: 1px solid black;">
                    Diskon 1
                </td>
                <td class="border border-black font-bold p-1 text-right" style="border: 1px solid black;">
                    Diskon 2
                </td>
                <td class="border border-black font-bold p-1 text-right" style="border: 1px solid black;">
                    Amount Diskon
                </td>
                <td class="border border-black font-bold p-1 text-right" style="border: 1px solid black;">
                    Subtotal
                </td>
            </tr>
        </thead>
        <tbody>
            @foreach ($items as $index => $item)
            <tr class="border border-black">
                <td class="border border-black p-1 max-w-sm" style="border: 1px solid black;">
                    {{ $index + 1 }}
                </td>
                <td class="border border-black p-1" style="border: 1px solid black;">
                    {{ $item->product->part_code }}
                </td>
                <td class="border border-black p-1" style="border: 1px solid black;">
                    {{ $item->product->name }}
                </td>
                <td class="border border-black p-1" style="border: 1px solid black;">
                    {{ $item->product->brand->name }}
                </td>
                <td class="border border-black p-1 text-right" style="border: 1px solid black;">
                    {{ formatIDR($item->qty) }}
                </td>
                <td class="border border-black p-1 text-right" style="border: 1px solid black;">
                    {{ formatIDR($item->price) }}
                </td>
                <td class="border border-black p-1 text-right" style="border: 1px solid black;">
                    {{ formatIDR($item->discount_percent_2) }}
                </td>
                <td class="border border-black p-1 text-right" style="border: 1px solid black;">
                    {{ formatIDR($item->discount_percent_1) }}
                </td>
                <td class="border border-black p-1 text-right" style="border: 1px solid black;">
                    {{ formatIDR($item->discount_total) }}
                </td>
                <td class="border border-black p-1 text-right" style="border: 1px solid black;">
                    {{ formatIDR($item->subtotal_discount) }}
                </td>
            </tr>
            @endforeach
            @foreach ([
            'Harga Jual' => $sale->amount_cost,
            'Diskon' => $sale->amount_discount,
            'Grand Total' => $sale->amount_cost - $sale->amount_discount,
            'DPP' => $sale->amount_net,
            'PPN' => $sale->amount_ppn,
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
    <table class="w-full pt-4 text-center mt-4">
        <tbody>
            <tr>
                <td class="text-center">Yang Menerima</td>
                <td class="text-center font-bold">Makassar, {{ formatDate($sale->s_date) }}</td>
            </tr>
            <tr>
                <td class="pt-10 text-center">
                    <pre>(         )</pre>
                </td>
                <td class="pt-10">Arwifan</td>
                <!-- {{ $sale->creator->name }} -->
            </tr>
        </tbody>
    </table>
    <table class="w-full pt-4 text-center mt-10">
        <tbody>
            <tr>
                <td class="text-left text-sm font-bold">
                    Note: <br />
                    Harga Grand Total sudah termasuk PPN 11%
                </td>
            </tr>

        </tbody>
    </table>
</body>

</html>