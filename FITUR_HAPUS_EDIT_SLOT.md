# Fitur Hapus dan Edit Slot Jadwal

## Fitur yang Ditambahkan

### 1. Edit Slot
- **Lokasi**: Halaman Admin > Jadwal & Slot
- **Fungsi**: Mengubah jam mulai, jam selesai, dan kapasitas slot
- **Tombol**: Ikon pensil (🖊️) pada setiap slot
- **Validasi**: 
  - Jam selesai harus setelah jam mulai
  - Kapasitas minimal sesuai jumlah booking yang sudah ada

### 2. Hapus Slot
- **Lokasi**: Halaman Admin > Jadwal & Slot  
- **Fungsi**: Menghapus slot yang belum memiliki booking
- **Tombol**: Ikon tempat sampah (🗑️) pada slot yang bisa dihapus
- **Kondisi**: 
  - Hanya bisa menghapus slot dengan 0 booking
  - Slot dengan booking akan menampilkan ikon gembok (🔒)

## Cara Penggunaan

### Edit Slot:
1. Buka halaman Admin > Jadwal & Slot
2. Pilih tanggal pada filter
3. Klik tombol edit (ikon pensil) pada slot yang ingin diubah
4. Ubah jam mulai, jam selesai, atau kapasitas
5. Klik "Update"

### Hapus Slot:
1. Buka halaman Admin > Jadwal & Slot
2. Pilih tanggal pada filter
3. Klik tombol hapus (ikon tempat sampah) pada slot yang ingin dihapus
4. Konfirmasi penghapusan

## File yang Dimodifikasi

1. **SlotController.php**
   - Menambah method `destroy()` untuk hapus slot
   - Mengupdate method `update()` untuk edit jam dan kapasitas

2. **web.php**
   - Menambah route DELETE untuk hapus slot

3. **jadwal-slot.blade.php**
   - Menambah tombol edit dan hapus pada setiap slot
   - Menambah modal edit slot
   - Menambah fungsi JavaScript untuk edit dan hapus

## Keamanan

- Validasi CSRF token pada semua request
- Validasi input waktu dan kapasitas
- Tidak bisa menghapus slot yang sudah memiliki booking
- Konfirmasi sebelum menghapus slot

## API Endpoints

- `PATCH /admin/jadwal-slot/slot/{id}` - Update slot
- `DELETE /admin/jadwal-slot/slot/{id}` - Hapus slot