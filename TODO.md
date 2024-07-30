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
-   [x] crud product/barang/part - normal (pakai table brands)
-   [x] halaman stock

-   [x] pemesanan po ( tidak berpengaruh ke stock )
        -> sudah ada sesuai from
    -   [x] tambah pemesanan
    -   [x] edit pemesanan
    -   [x] hapus pemenasan
    -   [x] daftar pemesanan
    -   [x] submit pemesanan
    -   [x] cetak pdf pemesanan -> sesuai form excel;
-   [x] pembelian
        x -> data diambil dari pemesanan , dapat input diskon dan harga beli
    -   [x] tambah
    -   [x] edit
    -   [x] hapus
    -   [x] daftar
    -   [x] submit (setelah submit stok masuk ke stock, stock fifo dan stock history)
    -   [x] cetak pembelian
-   [ ] penjualan
        -> data dari pembelian
    -   [ ] tambah
    -   [ ] edit
    -   [ ] hapus
    -   [ ] daftar
    -   [ ] submit (setelah submit stok masuk ke stock, stock fifo dan stock history)
    -   [ ] cetak pembelian / invoice
    -   [ ] cetak surat jalan
    -   [ ] cetak label
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
