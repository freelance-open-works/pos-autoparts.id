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

        body {
            margin: 0;
        }
    </style>
</head>

<body>
    <div class="font-bold">Pengirim : </div>
    <img src="{{ $setting->getStoragePath('app_logo') }}" width="180px" />
    <hr style="border: 1px solid black;" class="my-0.5" />
    <table class="">
        <tr>
            <td class="text-center p-1">
                <img src="{{ storage_path('/app/default/frigle1.png') }}" class="mb-2 object-fill" width="100px" />
                <img src="{{ storage_path('/app/default/frigle2.png') }}" class="mb-2 object-fill" width="100px" />
            </td>
            <td>
                <div class="font-sans">
                    <div class="font-bold">Penerima</div>
                    <span class="font-semibold">{{ $sale->customer->name }}</span>
                    <span class="mb-2 text-sm">{{ $sale->address }}</span>
                    <hr class="my-2" style="border: 1px solid black;" />

                    <div class="font-bold">Nomor Invoice</div>
                    <span class=" mb-2 w-full font-semibold">{{ $sale->s_code }}</span>
                    <hr class="my-2" style="border: 1px solid black;" />

                    <div class="font-bold">Nomor Surat Jalan</div>
                    <span class=" mb-2 font-semibold">{{ $delivery->sd_code }}</span>
                </div>
            </td>
        </tr>
    </table>
</body>

</html>