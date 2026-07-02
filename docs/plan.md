# PLAN.md — MAINTIFY (SERVTRACK)
### Rencana Eksekusi Pengembangan (Sprint Plan)

**Versi:** 1.0 | **Tanggal:** 02 Juli 2026
**Acuan:** PRD.md Bagian 46 (Milestone Development) & task-list.md

**Asumsi Tim Inti:** 1 Product Manager, 1 UI/UX Designer, 2 Frontend Developer, 2 Backend Developer, 1 QA Engineer, 1 DevOps Engineer.
**Metodologi:** Scrum, sprint 2 minggu (10 hari kerja).
**Total Durasi Proyek:** ± 25 minggu (± 6 bulan), terbagi dalam 9 Milestone dan 13 Sprint.

---

## 1. Ringkasan Milestone vs Sprint

| Milestone | Sprint | Fokus Utama |
|---|---|---|
| M1: Foundation & Autentikasi | Sprint 1–2 | Setup infrastruktur, database, autentikasi seluruh role |
| M2: Modul Kendaraan & QR Code | Sprint 2–3 | Tambah kendaraan, QR Code, dashboard pelanggan |
| M3: Modul Bengkel & Service | Sprint 3–4 | Scan QR, riwayat service, dashboard pegawai bengkel |
| M4: Modul Admin Bengkel | Sprint 5 | Kelola pegawai, pelanggan, laporan |
| M5: Bengkel Terdekat & Notifikasi | Sprint 6 | Peta lokasi, sistem pengingat |
| M6: Transfer Kepemilikan | Sprint 7 | Flow transfer end-to-end |
| M7: Modul Super Admin | Sprint 8–9 | Verifikasi bengkel, kelola pengguna, audit log |
| M8: QA, Security & UAT | Sprint 10–12 | Pengujian menyeluruh, hardening, UAT |
| M9: Deployment & Go-Live | Sprint 13 | Rilis produksi & monitoring awal |

---

## 2. Detail Sprint

### Sprint 0 — Persiapan (1 minggu, sebelum Sprint 1 dimulai)

**Tujuan:** Menyelaraskan tim terhadap PRD, menyiapkan tooling, dan memulai desain awal.

- [ ] Kick-off meeting: review PRD.md bersama seluruh tim
- [ ] Setup repository, project management board (task-list.md diimpor sebagai backlog)
- [ ] UI/UX mulai T-UX-001 s.d. T-UX-003 (wireframe Login, Register, Dashboard)
- [ ] DevOps mulai T-OPS-001 (setup infrastruktur dasar)
- [ ] Backend mulai T-BE-001, T-BE-002 (setup project & database sesuai erd.md)

---

### Sprint 1 (Minggu 1–2) — Foundation

**Milestone terkait:** M1

| Tim | Task |
|---|---|
| Backend | T-BE-001, T-BE-002, T-BE-003 (mulai) |
| Frontend | T-FE-001, T-FE-002 |
| UI/UX | T-UX-004 s.d. T-UX-009 |
| DevOps | T-OPS-001, T-OPS-002, T-OPS-003 (mulai) |
| QA | T-QA-001 (mulai susun test plan) |

**Sprint Goal:** Infrastruktur dasar siap, skema database ter-deploy di staging, wireframe halaman inti selesai.

---

### Sprint 2 (Minggu 3–4) — Autentikasi & Awal Modul Kendaraan

**Milestone terkait:** M1 (selesai), M2 (mulai)

| Tim | Task |
|---|---|
| Backend | T-BE-003 (selesai), T-BE-004, T-BE-005, T-BE-006 |
| Frontend | T-FE-003, T-FE-004, T-FE-015, T-FE-017 |
| UI/UX | T-UX-010 s.d. T-UX-013, mulai T-UX-016 (Design System) |
| DevOps | T-OPS-003 (selesai), T-OPS-004, T-OPS-005 |
| QA | T-QA-002 (test autentikasi begitu FE/BE selesai) |

**Sprint Goal:** Seluruh role dapat login/register; RBAC berfungsi; OTP Super Admin aktif.

---

### Sprint 3 (Minggu 5–6) — Modul Kendaraan & QR Code

**Milestone terkait:** M2 (selesai), M3 (mulai)

| Tim | Task |
|---|---|
| Backend | T-BE-007, T-BE-008, T-BE-009 |
| Frontend | T-FE-005, T-FE-006, T-FE-007, T-FE-009, T-FE-011 |
| UI/UX | T-UX-014, T-UX-015, T-UX-017 (mulai high-fidelity) |
| DevOps | T-OPS-006 |
| QA | T-QA-003 |

**Sprint Goal:** Pelanggan dapat menambahkan kendaraan, melihat dashboard, dan mengunduh QR Code kendaraan.

---

### Sprint 4 (Minggu 7–8) — Scan QR & Riwayat Service

**Milestone terkait:** M3 (selesai)

| Tim | Task |
|---|---|
| Backend | T-BE-010, T-BE-011 |
| Frontend | T-FE-008, T-FE-018, T-FE-019, T-FE-020, T-FE-021 |
| UI/UX | T-UX-017 (lanjut), T-UX-018 |
| DevOps | T-OPS-007 |
| QA | T-QA-004 |

**Sprint Goal:** Pegawai bengkel dapat memindai QR Code dan mencatat riwayat service secara end-to-end; pelanggan dapat melihat riwayat terbaru di dashboard.

---

### Sprint 5 (Minggu 9–10) — Modul Admin Bengkel

**Milestone terkait:** M4

| Tim | Task |
|---|---|
| Backend | T-BE-013 (mulai), T-BE-014, T-BE-018 (bagian Admin Bengkel), T-BE-020 |
| Frontend | T-FE-016, T-FE-022, T-FE-023, T-FE-024 |
| UI/UX | T-UX-017 (lanjut) |
| DevOps | T-OPS-008 (mulai) |
| QA | T-QA-007 (bagian Admin Bengkel) |

**Sprint Goal:** Admin Bengkel memiliki dashboard operasional, dapat mengelola pegawai, dan mengunduh laporan.

---

### Sprint 6 (Minggu 11–12) — Bengkel Terdekat & Notifikasi

**Milestone terkait:** M5

| Tim | Task |
|---|---|
| Backend | T-BE-012, T-BE-016 (mulai), T-BE-017 |
| Frontend | T-FE-010, T-FE-030 |
| UI/UX | T-UX-017 (lanjut/selesai) |
| DevOps | T-OPS-007 (lanjut), T-OPS-008 (lanjut) |
| QA | T-QA-006 |

**Sprint Goal:** Fitur pencarian bengkel terdekat berfungsi dengan integrasi peta; sistem pengingat service otomatis aktif.

---

### Sprint 7 (Minggu 13–14) — Transfer Kepemilikan

**Milestone terkait:** M6

| Tim | Task |
|---|---|
| Backend | T-BE-015, T-BE-016 (selesai) |
| Frontend | T-FE-013, T-FE-014 |
| UI/UX | T-UX-019 (usability testing paralel) |
| DevOps | — |
| QA | T-QA-005 (termasuk edge case Bagian 40 PRD) |

**Sprint Goal:** Flow transfer kepemilikan kendaraan berjalan end-to-end dengan seluruh validasi dan disclaimer.

---

### Sprint 8 (Minggu 15–16) — Modul Super Admin (Bagian 1)

**Milestone terkait:** M7 (mulai)

| Tim | Task |
|---|---|
| Backend | T-BE-013 (selesai), T-BE-019 (mulai) |
| Frontend | T-FE-025, T-FE-026 |
| QA | T-QA-008 (bagian Verifikasi Bengkel) |
| DevOps | T-OPS-009 |

**Sprint Goal:** Super Admin dapat memverifikasi/menolak pendaftaran bengkel mitra melalui dashboard sistem.

---

### Sprint 9 (Minggu 17–18) — Modul Super Admin (Bagian 2)

**Milestone terkait:** M7 (selesai)

| Tim | Task |
|---|---|
| Backend | T-BE-018 (bagian Super Admin, selesai), T-BE-019 (selesai) |
| Frontend | T-FE-027, T-FE-028, T-FE-029, T-FE-031 |
| QA | T-QA-008 (selesai) |
| DevOps | T-OPS-010 |

**Sprint Goal:** Seluruh modul Super Admin (kelola pengguna, kendaraan, bengkel, monitoring, audit log, pengaturan global) selesai dan terintegrasi.

---

### Sprint 10–12 (Minggu 19–24) — QA, Security Hardening & UAT

**Milestone terkait:** M8

| Tim | Fokus |
|---|---|
| Backend | T-BE-021, T-BE-022, T-BE-023, T-BE-024; perbaikan bug hasil QA |
| Frontend | T-FE-032; perbaikan bug hasil QA |
| QA | T-QA-009, T-QA-010, T-QA-011, T-QA-012, T-QA-013 |
| DevOps | T-OPS-011 (persiapan), T-OPS-012 |
| Seluruh Tim | Bug triage harian, retrospective per sprint |

**Sprint Goal:** Sistem lolos pengujian fungsional, non-fungsional, dan keamanan; UAT disetujui oleh stakeholder.

---

### Sprint 13 (Minggu 25) — Deployment & Go-Live

**Milestone terkait:** M9

| Tim | Task |
|---|---|
| DevOps | T-OPS-011 (deployment produksi & smoke test) |
| Seluruh Tim | Monitoring intensif pasca-launch (hypercare period 1 minggu) |
| Product Manager | Koordinasi go-live, komunikasi ke stakeholder & bengkel mitra awal |

**Sprint Goal:** Maintify resmi live di production dengan sistem monitoring aktif dan tim siaga menangani isu pasca-peluncuran.

---

## 3. Definition of Done (DoD)

Sebuah task/fitur dianggap **selesai** apabila memenuhi seluruh kriteria berikut:

1. Kode telah melalui code review oleh minimal 1 developer lain.
2. Unit test/integration test terkait telah dibuat dan lulus (untuk Backend).
3. Fitur telah diuji sesuai Acceptance Criteria terkait pada PRD Bagian 18.
4. Tidak ada bug kritikal (severity High/Critical) yang terbuka pada fitur tersebut.
5. UI telah sesuai dengan desain hi-fi dan responsif di desktop & mobile.
6. Dokumentasi API terkait (jika berlaku) telah diperbarui.
7. Fitur telah di-deploy dan diverifikasi berjalan normal di environment staging.

## 4. Ritual Scrum

| Ritual | Frekuensi | Peserta |
|---|---|---|
| Daily Standup | Setiap hari kerja, 15 menit | Seluruh tim development |
| Sprint Planning | Awal setiap sprint | Seluruh tim + Product Manager |
| Sprint Review/Demo | Akhir setiap sprint | Seluruh tim + Stakeholder |
| Sprint Retrospective | Akhir setiap sprint | Seluruh tim development |
| Backlog Grooming | Pertengahan sprint | Product Manager, Lead Frontend, Lead Backend |

## 5. Risk Buffer

Setiap Milestone memiliki buffer waktu implisit ±10% dari total estimasi task-list.md untuk mengantisipasi risiko yang telah diidentifikasi pada PRD Bagian 43 (Risk Analysis), seperti kompleksitas integrasi peta/geolokasi (M5) dan proses transfer kepemilikan yang bersifat transactional dan sensitif (M6).

## 6. Kriteria Go-Live (Exit Criteria M8 → M9)

- [ ] Seluruh Functional Requirement (FR-001–FR-113) telah diimplementasikan dan lulus test.
- [ ] Seluruh Non-Functional Requirement prioritas Must Have telah tervalidasi.
- [ ] Tidak ada open bug dengan severity Critical/High.
- [ ] UAT telah disetujui secara tertulis oleh Product Owner/stakeholder.
- [ ] Infrastruktur production, backup, dan monitoring telah aktif dan diverifikasi.
- [ ] Dokumentasi API dan panduan operasional dasar telah tersedia untuk tim support.
