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
            <td class="font-bold text-2xl text-right">SALE INVOICE</td>
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
                    Diskon (IDR)
                </td>
                <td class="border border-black font-bold p-1 text-right">
                    Diskon (%)
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
                    {{ formatIDR($item->price) }}
                </td>
                <td class="border border-black p-1 text-right">
                    {{ formatIDR($item->discount_amount) }}
                </td>
                <td class="border border-black p-1 text-right">
                    {{ formatIDR($item->discount_percent) }}
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
            'Harga Jual' => $sale->amount_cost + $sale->amount_discount,
            'Diskon' => $sale->amount_discount,
            'Grand Total' => $sale->amount_cost,
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
                <td class="text-center font-bold"></td>
            </tr>
            <tr>
                <td class="text-center text-xs"> </td>
                <td class="text-center font-bold">Makasar, {{ formatDate($sale->s_date) }}</td>
            </tr>
            <tr>
                <td class="text-center">
                    <pre>(         )</pre>
                </td>
                <td class="pt-10">{{ $sale->creator->name }}</td>
            </tr>
        </tbody>
    </table>
    <table class="w-full pt-4 text-center">
        <tbody>
            <tr>
                <td class="text-left text-sm font-bold">Harga Grand Total sudah termasuk PPN 11%</td>
            </tr>

        </tbody>
    </table>
</body>

</html>