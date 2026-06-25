# Maintify Product Requirements Document (PRD)

Status: Development

Product Name: Maintify

---

# 1. Technical Specification

## Technology Stack

### Backend

* Laravel 12
* PHP 8.3+

### Database

* Supabase PostgreSQL

### Frontend

* Blade
* TailwindCSS

### UI Components

* shadcn/ui inspired components

### Authentication

* Laravel Authentication
* Role Based Access Control

### QR Code

* Simple QrCode Package

### Storage

* Supabase Storage

---

## Architecture

### Application Pattern

Component Based Architecture

### Data Fetching

API Based Data Fetching

### Layout Structure

Sidebar + Topbar Dashboard Layout (Desktop)

Bottom Navigation (Mobile)

### Responsive Breakpoints

* sm: 640px
* md: 768px
* lg: 1024px
* xl: 1280px
* 2xl: 1536px

### Dark Mode

Supported by default.

---

# 2. Product Overview

## Product Description

Maintify adalah platform digital berbasis QR Code yang digunakan untuk menyimpan dan mengelola histori service kendaraan motor secara terpusat.

Setiap kendaraan memiliki QR Code unik yang berfungsi sebagai identitas digital kendaraan. QR Code tersebut dapat dipindai oleh bengkel mitra untuk mencatat histori service langsung ke dalam sistem.

Data service tersimpan secara digital sehingga pemilik kendaraan dapat memantau kondisi kendaraan kapan saja tanpa bergantung pada buku service fisik.

---

## Mission

Membantu pemilik kendaraan dan bengkel melakukan pencatatan serta monitoring histori service kendaraan secara digital, praktis, dan terintegrasi.

---

## Problem Statement

Permasalahan utama yang ingin diselesaikan:

* Buku service mudah hilang
* Riwayat service tidak terdokumentasi dengan baik
* Sulit mengetahui jadwal service berikutnya
* Sulit memantau kesehatan kendaraan
* Tidak ada sistem histori service terpusat

---

## Value Proposition

Maintify menyediakan identitas digital kendaraan berbasis QR Code yang memungkinkan histori service tersimpan secara aman, terpusat, dan mudah diakses.

---

# 3. User Roles

## Pemilik Kendaraan

Hak akses:

* Mengelola kendaraan
* Melihat QR kendaraan
* Melihat histori service
* Transfer kepemilikan kendaraan
* Melihat bengkel mitra

---

## Admin Bengkel

Hak akses:

* Scan QR kendaraan
* Input histori service
* Mengelola data service
* Mengelola data bengkel

---

## Super Admin

Hak akses:

* Mengelola pengguna
* Mengelola bengkel
* Mengelola kendaraan
* Monitoring seluruh aktivitas sistem

---

# 4. Core Features

## Authentication

* Login Pengguna
* Login Admin Bengkel
* Login Super Admin
* Registrasi Pengguna
* Registrasi Mitra Bengkel
* Forgot Password

---

## Dashboard

* Ringkasan kendaraan
* Health Score
* Jadwal service berikutnya
* Aktivitas service terbaru

---

## Vehicle Management

* Daftar kendaraan
* Tambah kendaraan
* Edit kendaraan
* Hapus kendaraan
* Detail kendaraan

---

## QR Vehicle Identity

* Generate QR Code
* Download QR
* Print QR
* QR Validation

---

## Service History

* Histori service
* Timeline service
* Filter service
* Detail service

---

## Workshop Directory

* Bengkel terdekat
* Detail bengkel
* Kontak bengkel

---

## Ownership Transfer

* Transfer kendaraan
* Approval transfer
* Riwayat tetap tersimpan

---

## Notifications

* Reminder service
* Reminder ganti oli
* Transfer notification

---

## Settings

* Profile
* Password
* Preferences

---

# 5. Navigation Structure

Beranda

My Vehicle

QR Code

Riwayat Service

Bengkel Terdekat

Settings

Profile

Logout

---

# 6. Screen Structure

## Login

Purpose:

Autentikasi pengguna.

Components:

* Email
* Password
* Login Button
* Forgot Password

Actions:

* Login
* Reset Password

---

## Register

Purpose:

Registrasi pengguna baru.

Components:

* Nama Lengkap
* Email
* Nomor HP
* Password
* Konfirmasi Password
* Domisili

Actions:

* Registrasi

---

## Dashboard

Purpose:

Menampilkan ringkasan kendaraan.

Components:

* Health Score Card
* Upcoming Service Card
* Vehicle Summary
* Recent Activity

Actions:

* View Vehicle
* View Service

---

## My Vehicle

Purpose:

Mengelola kendaraan pengguna.

Components:

* Search
* Vehicle Card
* Add Vehicle Button

Actions:

* Create
* Edit
* Delete
* Detail

---

## Detail Kendaraan

Purpose:

Menampilkan informasi kendaraan.

Components:

* Vehicle Information
* QR Code
* Service History
* Transfer Ownership

Actions:

* Edit Vehicle
* Transfer Ownership

---

## Barcode

Purpose:

Menampilkan QR kendaraan.

Components:

* QR Preview
* Download Button
* Print Button

Actions:

* Download QR
* Print QR

---

## Riwayat Service

Purpose:

Menampilkan histori service kendaraan.

Components:

* Service Timeline
* Filters
* Search
* Pagination

Actions:

* View Detail

---

## Bengkel Terdekat

Purpose:

Menampilkan daftar bengkel mitra.

Components:

* Search
* Workshop Cards
* Contact Button

Actions:

* View Detail
* Contact Workshop

---

## Transfer Kepemilikan

Purpose:

Memindahkan kepemilikan kendaraan.

Components:

* Vehicle Info
* New Owner Email
* Confirmation

Actions:

* Send Request

---

# 7. User Workflows

## Tambah Kendaraan

User Login

↓

My Vehicle

↓

Tambah Kendaraan

↓

Isi Form

↓

Simpan

↓

Generate QR

↓

Kendaraan Berhasil Ditambahkan

---

## Scan Service

Admin Bengkel Login

↓

Scan QR

↓

Detail Kendaraan

↓

Input Service

↓

Simpan

↓

Histori Service Tersimpan

---

## Transfer Kendaraan

Pemilik Lama

↓

Transfer Kepemilikan

↓

Masukkan Email Pemilik Baru

↓

Kirim Permintaan

↓

Pemilik Baru Menyetujui

↓

Transfer Berhasil

---

# 8. Data & Filtering Logic

## Search

Mendukung pencarian berdasarkan:

* Nama kendaraan
* Plat nomor
* Bengkel
* Jenis service

---

## Filters

* Status kendaraan
* Bengkel
* Jenis service
* Tanggal service

---

## Sorting

* Terbaru
* Terlama
* Kilometer tertinggi
* Kilometer terendah

---

## Pagination

Default:

10 item per halaman

---

# 9. UI Design System

## Design Style

Modern Automotive Platform

Dark Theme

Mobile First

Premium Dashboard

---

## Primary Color

#410008

---

## Secondary Color

#5E0B15

---

## Background

#121414

---

## Surface

#1E2020

---

## Border Radius

12px

---

## Shadow

Soft Shadow

---

## Typography

Inter

Modern

Readable

---

# 10. Layout Architecture

## Desktop

Sidebar

Topbar

Content Area

---

## Mobile

Top Navigation

Scrollable Content

Bottom Navigation

---

# 11. Component System

* Button
* Card
* Input
* Select
* Table
* Modal
* Tabs
* Tooltip
* Badge
* Pagination
* Filter Bar
* Date Picker
* QR Viewer

---

# 12. Analytics Guidelines

## KPI Cards

* Total Kendaraan
* Total Service
* Total Bengkel
* Health Score

## Charts

* Service Growth
* Vehicle Growth
* Workshop Growth

---

# 13. Responsive Behavior

## Mobile

390px

Primary Target

## Tablet

768px

Adaptive Layout

## Desktop

1440px

Sidebar Layout

---

# 14. Dark Mode

Default Theme

Background:

#121414

Cards:

#1E2020

Primary:

#410008

---

# 15. Analytics Metrics

## User

* Total Kendaraan
* Total Service
* Upcoming Service
* Health Score

## Workshop

* Total Service
* Active Customers

## Super Admin

* Total Users
* Total Vehicles
* Total Workshops
* Total Service Records
