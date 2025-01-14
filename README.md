# Sistem Approval Digital

Sistem Approval Digital adalah aplikasi berbasis web yang dirancang untuk mempermudah proses pengajuan dan persetujuan dokumen secara digital. Sistem ini mendukung alur kerja terstruktur, memudahkan kolaborasi antar-departemen, dan memberikan pelacakan riwayat persetujuan secara real-time.

---

## 🚀 Fitur Utama

### 1. **Manajemen Pengajuan (Submission)**
- Tambahkan pengajuan baru.
- Tampilkan daftar pengajuan dengan informasi lengkap, seperti:
  - **Bagian**, **No Transaksi**, **Judul Pengajuan**, **Remark**, **Status**, **Lampiran**, dan lainnya.
- Hapus pengajuan (opsional, tergantung role pengguna).

### 2. **Proses Persetujuan (Approval Process)**
- Alur persetujuan berbasis role:
  - **Check1**: Persetujuan awal.
  - **Check2**: Persetujuan lanjutan.
  - **ApprovalManager**: Persetujuan akhir.
- Setiap tahap persetujuan tercatat dengan status: **Pending**, **Approved**, atau **Rejected**.

### 3. **Riwayat Persetujuan (Approval History)**
- Lacak riwayat persetujuan berdasarkan ID pengajuan.
- Informasi yang tersedia:
  - **Bagian**, **No Transaksi**, **Tanggal Persetujuan**, **Remark**, **Approved By**, dan lainnya.

### 4. **Notifikasi SweetAlert**
- Menampilkan pemberitahuan interaktif saat:
  - Sesi pengguna berakhir.
  - Pengajuan atau persetujuan berhasil dilakukan.

---

## 📸 Tampilan Sistem

### **1. Halaman Submission**
- Menampilkan daftar pengajuan yang dibuat oleh pengguna.
- Fitur:
  - Tambah pengajuan baru.
  - Lihat status pengajuan: **Approved**, **Rejected**, atau **In Review**.
  - Hapus pengajuan.

![Halaman Submission](https://github.com/user-attachments/assets/800c8dbc-b665-4bf3-b639-e285fd614aa7)


---

### **2. Halaman Approval History**
- Menampilkan riwayat persetujuan untuk setiap pengajuan berdasarkan ID.
- Informasi lengkap terkait proses persetujuan.

![Halaman Approval History](https://github.com/user-attachments/assets/b3ced0e4-98a3-4668-a869-9d1f920a8d80)


---


