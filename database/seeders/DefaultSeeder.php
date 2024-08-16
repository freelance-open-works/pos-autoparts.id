<?php

namespace Database\Seeders;

use App\Constants\PermissionConstant;
use App\Models\Brand;
use App\Models\Customer;
use App\Models\Default\Permission;
use App\Models\Default\Role;
use App\Models\Default\Setting;
use App\Models\Default\User;
use App\Models\Expedition;
use App\Models\Product;
use App\Models\ProductStock;
use App\Models\Supplier;
use Carbon\Carbon;
use Exception;
use Illuminate\Database\Seeder;
use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Support\Str;
use Rap2hpoutre\FastExcel\FastExcel;
use Spatie\Async\Pool;
use Spatie\SimpleExcel\SimpleExcelReader;
use Symfony\Component\CssSelector\Node\FunctionNode;

class DefaultSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $settings = [
            ['id' => Str::ulid(), 'key' => 'app_name', 'value' => 'AUTOPART SALES', 'type' => 'text'],
            ['id' => Str::ulid(), 'key' => 'app_logo', 'value' => 'logo.png', 'type' => 'image'],
            ['id' => Str::ulid(), 'key' => 'company_name', 'value' => 'PT. AUTOPART SALES INDONESIA', 'type' => 'text'],
            ['id' => Str::ulid(), 'key' => 'company_address', 'value' => 'JL. Pattene Raya No. 3/4 , Sudiang, Makassar, Sulawesi Selatan', 'type' => 'text'],
            ['id' => Str::ulid(), 'key' => 'ppn_percent', 'value' => '1.11', 'type' => 'text'],
            ['id' => Str::ulid(), 'key' => 'monthly_sales_target', 'value' => '0', 'type' => 'text'],
        ];

        Setting::insert($settings);

        foreach (PermissionConstant::LIST as $permission) {
            Permission::insert(['id' => Str::ulid(), ...$permission]);
        }

        $role = Role::create(['name' => 'admin']);

        $permissions = Permission::all();
        foreach ($permissions as $permission) {
            $role->rolePermissions()->create(['permission_id' => $permission->id]);
        }

        User::create([
            'name' => 'Super Administrator',
            'email' => 'root@admin.com',
            'password' => bcrypt('password'),
            'additonal_fields' => json_encode([
                'code' => '000',
                'address' => '',
            ]),
        ]);

        User::create([
            'name' => 'Administator',
            'email' => 'admin@admin.com',
            'password' => bcrypt('password'),
            'role_id' => $role->id,
            'additonal_fields' => json_encode([
                'code' => '001',
                'address' => '',
            ]),
        ]);

        $sales = Role::create(['name' => Role::SALES]);
        $permissions = Permission::where('name', 'like', '%sale')->orWhere('name', 'like', '%purchase%')->get();
        foreach ($permissions as $permission) {
            $sales->rolePermissions()->create(['permission_id' => $permission->id]);
        }

        User::create([
            'name' => 'ANDI MUHAMMAD SYUKUR YUSUF',
            'email' => 'andi@admin.com',
            'password' => bcrypt('password'),
            'role_id' => $sales->id,
            'additonal_fields' => json_encode([
                'code' => 'S01',
                'address' => 'MAKASSAR',
            ]),
        ]);

        User::create([
            'name' => 'CLAUDIUS AMAL',
            'email' => 'amal@admin.com',
            'password' => bcrypt('password'),
            'role_id' => $sales->id,
            'additonal_fields' => json_encode([
                'code' => 'C02',
                'address' => 'MAKASSAR',
            ]),
        ]);

        $this->expeditions();
        $this->customers();
        $this->suppliers();
        $this->brands();
        $this->products();
    }

    public function expeditions()
    {
        $expeditions = [
            ['name' => 'Sinar Surya', 'address' => 'JL. Langgau No. 41B'],
            ['name' => 'Land Transport Expedition Gantos', 'address' => 'JL. Ballang Lompo Ruko No. 4A'],
            ['name' => 'Mitra Surya', 'address' => 'JL. Ballang Caddi Makassar'],
            ['name' => 'Tujuh Jaya Ekspedisi', 'address' => 'JL. Teuku Umar Raya No. 111-113, Makassar'],
            ['name' => 'Meteor Trans', 'address' => 'Jl. Gatot Subroto No.20, Ujung Pandang Baru, MKS'],
            ['name' => 'Abadi Trans', 'address' => 'Jln. Irian No. 219, Makassar'],
            ['name' => 'Lintas Morowali', 'address' => 'Ruko Cakalang Indah Blok G No.3T, Jl. Cakalang, MKS'],
            ['name' => 'Wajo Raya Exp', 'address' => 'Jl. Ir. Sutami Toll (Samping Terowongan 2 TOLL)'],
            ['name' => 'Pamona Trans', 'address' => 'Jln. Teuku Umar Raya No.118 E'],
            ['name' => 'Tangan Rahmat Exp', 'address' => 'Jl. Sultan Abdullah Raya No.29 A, Makassar'],
        ];

        foreach ($expeditions as $e) {
            Expedition::create($e);
        }
    }

    public function customers()
    {
        $customers = [
            ['code' => 'SMR-01', 'name' => 'Sumber Motor Rantepao', 'address' => 'JL. Diponegoro No. 44, Penanian, Rantepao', 'type' => Customer::OUTCITY],
            ['code' => 'BZM-02', 'name' => 'Bengkel Zhafira Masamba', 'address' => 'Jl. Trans Sulawesi', 'type' => Customer::OUTCITY],
            ['code' => 'AMM-03', 'name' => 'Anugerah Motor Makale', 'address' => 'Jl. Pongtiku, Lion Tondok Iring', 'type' => Customer::OUTCITY],
            ['code' => 'SMB-04', 'name' => 'Toko Sahabat Motor', 'address' => ' Bahodopi, Morowali, Sulawesi Tengah', 'type' => Customer::OUTCITY],
            ['code' => 'RJM-05', 'name' => 'Toko Radar Jaya Mandiri', 'address' => 'Jl. Trans Sulawesi, Bawalipu, Wotu, Lutim', 'type' => Customer::OUTCITY],
            ['code' => 'MKT-06', 'name' => 'Toko Mitra Kencana', 'address' => 'Jl. Trans Sulawesi, Tomoni', 'type' => Customer::OUTCITY],
            ['code' => 'DJB-07', 'name' => 'Toko Dilma Jaya Bayondo', 'address' => 'Bayondo, Kec. Tomoni, Luwu ', 'type' => Customer::OUTCITY],
            ['code' => 'GMP-08', 'name' => 'Toko Galesong Motor', 'address' => 'Jl. Mangga No.80, Lagaligo, Palopo', 'type' => Customer::OUTCITY],
            ['code' => 'SOB-09', 'name' => 'Toko Setia Otopart Bone - Bone', 'address' => 'Jl. Trans Sulawesi, Patoloan', 'type' => Customer::OUTCITY],
            ['code' => 'TJM-10', 'name' => 'Toko Tiar Jaya Motor', 'address' => 'Samping Mako Brimob, Depan Cafe Wajo, Tabo, Labota, Bahodopi', 'type' => Customer::OUTCITY],
            ['code' => 'DMS-11', 'name' => 'Toko Dian Motor', 'address' => 'Suppa - Siwa', 'type' => Customer::OUTCITY],
            ['code' => 'DMK-12', 'name' => 'Toko Dilla Motor', 'address' => 'Ballere, Kec Keera, Kab Wajo', 'type' => Customer::OUTCITY],
            ['code' => 'ART-13', 'name' => 'Toko Arif Motor Takalar', 'address' => 'Jln. Poros Takalar', 'type' => Customer::OUTCITY],
            ['code' => 'HMM-14', 'name' => 'H. MOCTHAR', 'address' => 'Masamba', 'type' => Customer::OUTCITY],
            ['code' => 'SON-15', 'name' => 'TOKO SINAR OMPO', 'address' => 'MAKASSAR', 'type' => Customer::INCITY],
            ['code' => 'BAV-16', 'name' => 'BENGKEL ANZHO VARIASI', 'address' => 'MAKASSAR', 'type' => Customer::INCITY],
            ['code' => 'CMM-17', 'name' => 'TOKO CELEBES MOTOR', 'address' => 'MAKASSAR', 'type' => Customer::INCITY],
            ['code' => 'SMD-18', 'name' => 'BENGKEL SUMBER MAS DAYA', 'address' => 'MAKASSAR', 'type' => Customer::INCITY],
            ['code' => 'CFG-19', 'name' => 'CV. FAIS GROUP', 'address' => 'MAROS', 'type' => Customer::INCITY],
            ['code' => 'BJM-20', 'name' => 'TOKO BANDANG JAYA', 'address' => 'MAKASSAR', 'type' => Customer::INCITY],
            ['code' => 'BAM-21', 'name' => 'BENGKEL AS MOTOR', 'address' => 'MAKASSAR', 'type' => Customer::INCITY],
            ['code' => 'WJM-22', 'name' => 'BENGKEL WENING JAYA MOTOR', 'address' => 'MAROS', 'type' => Customer::INCITY],
            ['code' => 'SMM-23', 'name' => 'TOKO SUMBER MAS MOTOR', 'address' => 'MAKASSAR', 'type' => Customer::INCITY],
            ['code' => 'SOM-24', 'name' => 'TOKO SUPER OLI', 'address' => 'MAKASSAR', 'type' => Customer::INCITY],
            ['code' => 'S01', 'name' => 'FLEET USER (SYUKUR)', 'address' => 'MAKASSAR', 'type' => Customer::INCITY],
            ['code' => 'C02', 'name' => 'FLEET USER (CLAUS)', 'address' => 'MAKASSAR', 'type' => Customer::INCITY],
        ];

        foreach ($customers as $c) {
            Customer::create($c);
        }
    }

    public function suppliers()
    {
        $suppliers = [
            ['code' => 'SUP-01', 'name' => 'Example', 'address' => 'Example Address', 'type' => Customer::OUTCITY],
            ['code' => 'SUP-02', 'name' => 'Suppliers ', 'address' => 'JL. Diponegoro No. 44, Penanian, Rantepao', 'type' => Customer::OUTCITY],
        ];

        foreach ($suppliers as $s) {
            Supplier::create($s);
        }
    }

    public function brands()
    {
        $brands = [
            ['name' => 'DAIHATSU'],
            ['name' => 'ISUZU'],
            ['name' => 'TOYOTA'],
            ['name' => 'SUZUKI'],
            ['name' => 'DENSO'],
            ['name' => 'TDW'],
            ['name' => 'MITSUBISHI'],
        ];

        foreach ($brands as $b) {
            Brand::create($b);
        }
    }

    public function products()
    {
        $startTime = now();
        info(self::class, ['start import products', $startTime]);
        $brands = Brand::all()->mapWithKeys(function ($b) {
            return [$b->name => $b->id];
        });

        $path = storage_path('app/default/DATABASE SPAREPART.xlsx');
        $pool = Pool::create();
        foreach (range(1, 11) as $n) { // ~250s
            $config = config('database.connections.sqlite');
            $pool->add(function () use ($n, $path, $brands, $config) {
                try {
                    syslog(LOG_INFO, 'Chunk = ' . $n . ' Start');
                    // recreate eloquent connection on every threads created
                    $capsule = new Capsule;
                    $capsule->addConnection($config);
                    $capsule->setAsGlobal();
                    $capsule->bootEloquent();

                    $now = Carbon::now();
                    $sheet = (new FastExcel())->sheet($n)->import($path);
                    syslog(LOG_INFO, 'Chunk = ' . $n . ' ; Read Total : ' . count($sheet) . "; Time : " . $now->diffInSeconds(Carbon::now()) . "s");

                    $ulid = fn() => Str::ulid();
                    $products = [];

                    foreach ($sheet as $r) {
                        $products[] = [
                            'id' => $ulid(),
                            'name' => Str::trim($r['Nama Barang']),
                            'part_code' => Str::trim($r['Part No']),
                            'type' => Str::trim($r['Type Barang']),
                            'discount' => Str::trim($r['Discount']),
                            'cost' => Str::trim($r['Harga Beli']),
                            'price' => Str::trim($r['Harga Jual']),
                            'brand_id' => $brands[Str::trim($r['Merk'])] ?? $brands[0],
                        ];
                    }

                    foreach (array_chunk($products, 5000) as $ps) {
                        Product::insert($ps);
                        // convert to product_stocks and insert it
                        $stocks = array_map(function ($item) {
                            return [
                                'id' => $item['id'],
                                'product_id' => $item['id'],
                                'stock' => '0'
                            ];
                        }, $ps);
                        ProductStock::insert($stocks);
                    }
                } catch (Exception $e) {
                    syslog(LOG_INFO, 'Chunk = ' . $n . ' ; Error : ' . json_encode($e->getMessage()));
                }

                syslog(LOG_INFO, 'Chunk = ' . $n . ' Done');
            },)->catch(function ($exception) {
                info(self::class, [$exception]);
            });
        }

        $pool->wait();
        sleep(5);
        syslog(LOG_INFO, '============| IMPORT DONE |==============');

        info(self::class, ['done import products', Product::count(), now(), $startTime->diffInSeconds(now())]);
    }
}
