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
            <td class="font-bold text-2xl text-right">SALES ORDER</td>
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
            <td class="font-bold capitalize">NO</td>
            <td class="px-2">:</td>
            <td> {{ $store_order->so_code }}</td>
        </tr>
        <tr>
            <td class="font-bold capitalize">Tanggal</td>
            <td class="px-2">:</td>
            <td> {{ formatDate($store_order->so_date) }}</td>
        </tr>
        <tr>
            <td class="font-bold capitalize">Nama Customer</td>
            <td class="px-2">:</td>
            <td> {{ $store_order->customer->name }}</td>
        </tr>
        <tr>
            <td class="font-bold capitalize">Alamat Customer</td>
            <td class="px-2">:</td>
            <td> {{ $store_order->address }}</td>
        </tr>
    </table>
    <table class="border-collapse border border-black w-full solid-table">
        <thead>
            <tr class="border border-black">
                <td class="border border-black font-bold p-2 max-w-sm">
                    No
                </td>
                <td class="border border-black font-bold p-2">
                    Part No
                </td>
                <td class="border border-black font-bold p-2">
                    Part Name
                </td>
                <td class="border border-black font-bold p-2">
                    Merek
                </td>
                <td class="border border-black font-bold p-2 text-right">
                    Qty
                </td>
                <!-- <td class="border border-black font-bold p-2 text-right">
                    Harga
                </td>
                <td class="border border-black font-bold p-2 text-right">
                    Subtotal
                </td> -->
            </tr>
        </thead>
        <tbody>
            @foreach ($items as $index => $item)
            <tr class="border border-black">
                <td class="border border-black p-2 max-w-sm">
                    {{ $index + 1 }}
                </td>
                <td class="border border-black p-2">
                    {{ $item->product->part_code }}
                </td>
                <td class="border border-black p-2">
                    {{ $item->product->name }}
                </td>
                <td class="border border-black p-2">
                    {{ $item->product->brand->name }}
                </td>
                <td class="border border-black p-2 text-right">
                    {{ formatIDR($item->qty) }}
                </td>
                <!-- <td class="border border-black p-2 text-right">
                    {{ formatIDR($item->cost) }}
                </td>
                <td class="border border-black p-2 text-right">
                    {{ formatIDR($item->qty * $item->cost) }}
                </td> -->
            </tr>
            @endforeach

        </tbody>
    </table>
    <table class="w-full pt-4 text-center">
        <tbody>
            <tr>
                <td class="text-left text-sm">Keterangan : </td>
                <td class="font-bold text-right text-xl">Total Qty : {{ formatIDR($store_order->items()->sum('qty')) }}</td>
            </tr>
            <tr>
                <td class="text-left" style="width: 200px;">
                    <pre class="font-sans text-sm" style="width: 200px;">{{ $store_order->note }}</pre>
                </td>
            </tr>
        </tbody>
    </table>
    <table class="w-full pt-4 text-center mt-4">
        <tbody>
            <tr>
                <td class="text-left" style="width: 400px;">
                    <!-- <pre class="font-sans text-xs">- Semua pengiriman barang harus disertakan Nota/Faktur
- Barang akan kami kembalikan jika tidak sesuai PO -->
                    </pre>
                </td>
                <td class="text-center font-bold"></td>
            </tr>
            <tr>
                <td class="text-left text-xs"> </td>
                <td class="text-center font-bold">Nama Customer</td>
            </tr>
            <tr>
                <td class=""></td>
                <td class="pt-10">{{ $store_order->customer->name }}</td>
            </tr>
        </tbody>
    </table>
</body>

</html>