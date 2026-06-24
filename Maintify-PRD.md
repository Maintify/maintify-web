# Maintify - Product Requirements Document (PRD)

# 1. Technical Specification

## Technology Stack

### Frontend

* Laravel 12
* Blade Template Engine
* TailwindCSS
* Alpine.js

### UI Framework

* TailwindCSS

### Component Library

* shadcn/ui style components
* Headless UI
* Lucide Icons

### Backend & Database

* Supabase PostgreSQL

### Authentication

* Supabase Auth
* Email & Password Login
* Password Reset
* Session Management
* Role-Based Access Control (RBAC)

### Storage

* Supabase Storage

### Analytics

* ApexCharts / Chart.js

### QR Code

* Unique QR Code per kendaraan
* QR Code generation
* QR Code download & print

## Architecture

User Browser

↓

Laravel Application

↓

Supabase API

↓

PostgreSQL Database

## Dashboard Layout

Sidebar + Topbar Layout

### Sidebar Menu

* Dashboard
* Kendaraan
* QR Code
* Histori Service
* Bengkel
* Analytics
* Pengaturan

### Topbar

* Search
* Notification
* User Profile
* Dark Mode Toggle

## Responsive Breakpoints

* sm: 640px
* md: 768px
* lg: 1024px
* xl: 1280px
* 2xl: 1536px

---

# 2. Product Overview

## Product Name

Maintify

## Product Type

Vehicle Service History Management Platform

## Industry

Automotive Technology

## Target Users

### Pemilik Kendaraan

Pemilik kendaraan motor yang ingin menyimpan histori service secara digital.

### Bengkel Mitra

Bengkel yang melakukan pencatatan service kendaraan secara digital.

### Administrator

Pengelola sistem Maintify.

## Mission

Menyediakan platform digital berbasis QR Code untuk pencatatan dan monitoring histori service kendaraan motor secara terpusat.

## Problem Statement

Sebagian besar histori service kendaraan masih dicatat secara manual sehingga:

* Data mudah hilang
* Sulit dipantau
* Tidak terintegrasi antar bengkel
* Sulit mengetahui kondisi kendaraan

## Value Proposition

Setiap kendaraan memiliki QR Code unik yang dapat dipindai oleh bengkel untuk mencatat histori service secara langsung ke dalam sistem.

---

# 3. Core Features

## Dashboard

Menampilkan ringkasan kendaraan dan aktivitas service.

## Vehicle Management

Mengelola data kendaraan.

## QR Code Management

Pembuatan dan pengelolaan QR Code kendaraan.

## Service History

Pencatatan dan monitoring histori service.

## Vehicle Health Monitoring

Monitoring kesehatan kendaraan berdasarkan histori service.

## Workshop Management

Pengelolaan bengkel mitra.

## Notifications

Pengingat service berkala.

## Reports & Analytics

Visualisasi data service.

## Settings

Pengaturan akun dan sistem.

---

# 4. Navigation Structure

Dashboard

├── Kendaraan

│ ├── Daftar Kendaraan

│ ├── Tambah Kendaraan

│ └── Detail Kendaraan

├── QR Code

│ └── Detail QR Code

├── Histori Service

│ ├── Daftar Service

│ └── Detail Service

├── Bengkel

│ ├── Daftar Bengkel

│ └── Detail Bengkel

├── Analytics

└── Pengaturan

---

# 5. User Roles & Permissions

## Super Admin

Dapat:

* Mengelola seluruh data
* Mengelola bengkel
* Mengelola pengguna
* Mengakses laporan

## Bengkel Mitra

Dapat:

* Scan QR kendaraan
* Menambah histori service
* Mengedit histori service miliknya
* Melihat kendaraan yang pernah diservice

## Pemilik Kendaraan

Dapat:

* Mengelola kendaraan sendiri
* Melihat QR Code
* Melihat histori service
* Melihat status kesehatan kendaraan

---

# 6. Screen Structure

## Login

### Purpose

Autentikasi pengguna.

### Components

* Email Input
* Password Input
* Login Button
* Forgot Password

### Actions

* Login
* Reset Password

---

## Dashboard

### Purpose

Ringkasan informasi kendaraan.

### Components

* KPI Cards
* Vehicle Status
* Upcoming Service
* Recent Activities
* Charts

### Actions

* Lihat Detail
* Filter Data

---

## Vehicle List

### Purpose

Menampilkan daftar kendaraan.

### Components

* Search Bar
* Filter
* Vehicle Table
* Add Vehicle Button

### Actions

* Create
* Edit
* Delete
* View Detail

---

## Vehicle Detail

### Purpose

Informasi lengkap kendaraan.

### Components

* Vehicle Information
* QR Code
* Service History
* Health Score

### Actions

* View History
* Download QR

---

## QR Code Detail

### Purpose

Menampilkan QR kendaraan.

### Components

* QR Preview
* Download Button
* Print Button

### Actions

* Download
* Print

---

## Service History

### Purpose

Menampilkan histori service.

### Components

* Service Table
* Filter
* Search
* Pagination

### Actions

* View Detail
* Filter Data

---

## Add Service

### Purpose

Menambahkan histori service.

### Components

* Scan QR
* Service Form
* Odometer Input
* Notes
* Cost

### Actions

* Save Service
* Edit Service

---

## Analytics

### Purpose

Visualisasi data kendaraan dan service.

### Components

* KPI Cards
* Line Chart
* Bar Chart
* Donut Chart

### Actions

* Filter Period
* Export Report

---

# 7. Features Per Screen

## Vehicle Management

### Create Vehicle

1. Klik Tambah Kendaraan
2. Isi data kendaraan
3. Simpan
4. Sistem membuat QR Code otomatis

### Edit Vehicle

1. Buka Detail Kendaraan
2. Edit Data
3. Simpan

### Delete Vehicle

1. Klik Hapus
2. Konfirmasi
3. Data dihapus

---

## Service History

### Create Service

1. Bengkel scan QR
2. Sistem menampilkan kendaraan
3. Isi data service
4. Simpan histori

### Edit Service

1. Buka detail service
2. Edit data
3. Simpan

### View Detail

1. Pilih service
2. Tampilkan detail

---

# 8. Data & Filtering Logic

## Search

Pencarian berdasarkan:

* Nomor Polisi
* Nama Pemilik
* Bengkel
* Jenis Service

## Filters

### Kendaraan

* Semua
* Aktif
* Perlu Service

### Service

* Ganti Oli
* Tune Up
* Servis Berkala
* Perbaikan

## Sorting

* Terbaru
* Terlama
* Kilometer Tertinggi
* Kilometer Terendah

## Date Range

* Hari Ini
* 7 Hari
* 30 Hari
* 90 Hari
* Custom

## Pagination

* 10
* 25
* 50
* 100

## Summary Text Logic

Menampilkan histori service kendaraan berdasarkan filter yang dipilih pengguna.

---

# 9. UI Design System

## Visual Style

Modern SaaS Dashboard

## Spacing

* 4px
* 8px
* 12px
* 16px
* 24px
* 32px

## Border Radius

* Card: 16px
* Input: 12px
* Button: 12px

## Card Style

* White background
* Soft shadow
* Rounded corners

## Table Style

* Sticky header
* Zebra hover
* Responsive scroll

## Form Style

* Single column mobile
* Two columns desktop

## Typography

* Inter Font
* Clear hierarchy

---

# 10. Color System

## Primary

#410008

Digunakan untuk:

* Sidebar
* Primary Button
* Active Menu

## Secondary

#6D0013

## Light Accent

#F5E8EB

## Neutral

* #FFFFFF
* #F8F9FA
* #E5E7EB
* #6B7280
* #111827

## Status Colors

Success: #10B981

Warning: #F59E0B

Error: #EF4444

Info: #3B82F6

---

# 11. Layout Architecture

## Sidebar

Navigasi utama sistem.

## Topbar

Search, Notification, Profile.

## Content Area

Responsive grid layout.

### Max Width

1440px

### Spacing

24px

---

# 12. Component System

* Table
* Card
* Modal
* Drawer
* Button
* Input
* Select
* Tabs
* Badge
* Tooltip
* Pagination
* Filter Bar
* Date Picker
* Charts

---

# 13. Chart & Analytics Guidelines

## KPI Cards

* Total Kendaraan
* Total Service
* Total Bengkel
* Kendaraan Perlu Service

## Charts

### Line Chart

Tren service bulanan.

### Bar Chart

Jumlah service per bengkel.

### Donut Chart

Distribusi jenis service.

---

# 14. Responsive Behavior

## Desktop

Sidebar expanded.

## Tablet

Sidebar collapsed.

## Mobile

Hamburger menu.

Table menjadi horizontal scroll.

---

# 15. Dark Mode

## Background

Dark gray.

## Text

Light gray.

## Cards

Dark surface.

## Charts

Adaptive color palette.

---

# 16. Analytics Metrics

## Admin

* Total User
* Total Kendaraan
* Total Bengkel
* Total Service

## Bengkel

* Kendaraan Dilayani
* Service Bulanan
* Pelanggan Aktif

## Pemilik Kendaraan

* Total Kendaraan
* Total Service
* Upcoming Service
* Health Score
