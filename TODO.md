testi https://tastiapp.tasti.id/Tasti-new/login
User name : IDHAM.GOWA
Pass : 1a2b3c4d

kalau admin semua fitur yang ada diatas
kalau sales cukup pemesanan, pembelian, penjualan dan claim

sepertinya dari po sampai penjualan adalah 1 alur
PO -> Pembelian (stock masuk) -> Penjualan (stok keluar)

status:

-   draft [manual]
-   diproses [manual]
-   disubmit [auto]
-   selesai [manual]

claim (pengembalian) -> berdasarkan invoice penjualan nanti bisa di filter pakai customernya

-   [x] crud expedisi - normal
-   [x] crud customer - normal
-   [x] crud supplier - normal
-   [ ] crud product/barang/part - normal (pakai table brands)
-   [ ] halaman stock

-   [ ] pemesanan po ( tidak berpengaruh ke stock )
        -> sudah ada sesuai from
    -   [ ] tambah pemesanan
    -   [ ] edit pemesanan
    -   [ ] hapus pemenasan
    -   [ ] daftar pemesanan
    -   [ ] submit pemesanan
    -   [ ] cetak pdf pemesanan -> sesuai form excel;
-   [ ] pembelian
        -> data diambil dari pemesanan , dapat input diskon dan harga beli
    -   [ ] tambah
    -   [ ] edit
    -   [ ] hapus
    -   [ ] daftar
    -   [ ] submit (setelah submit stok masuk ke stock, stock fifo dan stock history)
    -   [ ] cetak pembelian
-   [ ] penjualan
        -> data dari pembelian
    -   [ ] tambah
    -   [ ] edit
    -   [ ] hapus
    -   [ ] daftar
    -   [ ] submit (setelah submit stok masuk ke stock, stock fifo dan stock history)
    -   [ ] cetak pembelian
-   [ ] pengembalian
        -> ambil data dari penjualan [filter by customer]
    -   [ ] tambah
    -   [ ] edit
    -   [ ] hapus
    -   [ ] daftar
    -   [ ] submit (setelah submit stok masuk ke stock, stock fifo dan stock history)
    -   [ ] cetak pembelian
-   [ ] laporan pembelian (index, filter: tanggal, supplier, export excel)
-   [ ] laporan penjualan (index, filter: tanggal, customer, export excel)
