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
            <td class="font-bold text-2xl text-right">RETURN</td>
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
            <td> {{ $claim->customer->name }}</td>
            <td class="font-bold capitalize">Invoice Number</td>
            <td class="px-2">:</td>
            <td> {{ $claim->c_code }}</td>
        </tr>
        <tr>
            <td class="font-bold capitalize">Address</td>
            <td class="px-2">:</td>
            <td> {{ $claim->sale->address }}</td>
            <td class="font-bold capitalize">Invoice Date</td>
            <td class="px-2">:</td>
            <td> {{ formatDate($claim->c_date) }}</td>
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
            </tr>
            @endforeach
        </tbody>
    </table>
</body>

</html>