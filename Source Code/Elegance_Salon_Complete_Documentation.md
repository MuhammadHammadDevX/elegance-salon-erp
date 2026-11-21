# ELEGANCE SALON MANAGEMENT & LUXURY CLIENT PORTAL
## Complete Technical Specification, System Manual & Project Documentation

---

### Project Metadata
- **Project Title:** Elegance Salon Karachi — Luxury Men's Grooming Lounge & Enterprise Salon ERP
- **Location:** Main Tariq Road, Block 2 PECHS, Karachi, Sindh, Pakistan
- **System Architecture:** Modular MVC / Procedural Multi-Tier Architecture
- **Primary Tech Stack:** PHP 8.x, MySQL (InnoDB), JavaScript (ES6+), CSS3 (Glassmorphism), Bootstrap 5.3, Thermal POS Printing Engine, WhatsApp Webhook Dispatcher
- **Target Audience:** Luxury Clients, Master Barbers, Reception Cashiers, Salon Owners & Investors

---

## TABLE OF CONTENTS
1. Executive Summary & Problem Statement
2. High-Level System Architecture
3. Luxury Client Portal (User-Side Specifications - 4 Figures)
   - 3.1 Hero Section & Value Proposition (Figure 1)
   - 3.2 About Elegance & Brand Story (Figure 2)
   - 3.3 Master Stylists & Barber Roster (Figure 3)
   - 3.4 Frequently Asked Questions FAQ (Figure 4)
   - 3.5 Service Catalog & Signature Treatment Breakdown
   - 3.6 Bespoke Booking Engine (appointment.php)
   - 3.7 Client Authentication & Personal Dashboard (dashboard.php)
4. Executive Admin Dashboard (Management Specifications - 4 Figures)
   - 4.1 Executive Overview, Financials & Revenue Graph (Figure 5)
   - 4.2 Enterprise User Management & Role Guard (Figure 6)
   - 4.3 Service Catalog CRUD & Dynamic Pricing (Figure 7)
   - 4.4 Stylists & Master Barbers Commission Management (Figure 8)
   - 4.5 1-Click CSV / Excel Sales Report Export
   - 4.6 Commercial Inventory & WhatsApp Supplier Reorders
5. Front Desk Reception Terminal (Floor Operations - 1 Figure)
   - 5.1 Live Floor & 6-Chair Occupancy Engine (Figure 9)
   - 5.2 Real-Time Karachi Digital Clock & Floor Health
   - 5.3 Fast Walk-In Registration & Dynamic Discount Calculator
   - 5.4 1-Click Direct WhatsApp Client Alert
   - 5.5 80mm Thermal POS Receipt Slip Printer
6. Database Architecture & Entity Relationship Diagram (Figure 10)
7. Security & Role-Based Access Control (RBAC)
8. Installation, Deployment & Local Setup Manual
9. Viva Voce & Presentation Defense Guide

---

## 1. Executive Summary & Problem Statement
In high-volume commercial luxury salons across Pakistan (specifically on Main Tariq Road, Clifton, and DHA Karachi), traditional paper registers create critical operational bottlenecks: double-booked chairs, unrecorded walk-in revenues, barber commission disputes, and inventory stockouts.

**The Elegance Solution:** A unified digital ecosystem bridging an ultra-luxury client reservation portal with an operational Front Desk Reception Console (6-chair occupancy grid, thermal receipt printing, 1-click WhatsApp alerts) and an Executive Admin Control Panel (automated 18% barber commissions, live gross sales metrics, and 1-click Excel CSV sales export).

## 2. Luxury Client Portal (User-Side Specifications)
### 2.1 Hero Section & Value Proposition (Figure 1)
![Elegance Hero Section](assets/userside_hero_section.png)
- **Headline:** 'Award Winning Male Grooming' in bespoke typography.
- **Conversion Incentive:** '-15% off For your very first visit' pill badge.
- **Social Proof:** '★★★★★ 4.9 | Loved by 1k+ trusted clients' with client avatar badges.
- **Direct CTAs:** 'Book Your Slot' (routes to appointment.php) and 'Learn More'.

### 2.2 About Elegance & Brand Story (Figure 2)
![About Elegance](assets/userside_about_section.png)
- **Section Badge:** ABOUT ELEGANCE
- **Section Heading:** 'Elegance Salon: The Premier Men\'s Salon & Barbershop in Karachi'
- **Focus & Goals:** 'Revolutionizing the Karachi Barbershop experience through technical grooming.'

### 2.3 Master Stylists & Barber Roster (Figure 3)
![Meet the Stylists](assets/userside_stylists_section.png)
Features senior master barbers: Asif (Master Barber & Beard Stylist), Zafar (Skin Treatments & Grooming), Aliyan (Skin Treatments & Contemporary Styling), and Zachary Nickell / Sufiyan.

### 2.4 Frequently Asked Questions FAQ (Figure 4)
![FAQ Section](assets/userside_faq_section.png)
Provides immediate answers regarding location (Main Tariq Road PECHS), booking policies, treatment durations, and retail products.

## 3. Executive Admin Dashboard (Management Specifications)
### 3.1 Executive Overview, Financials & Revenue Graph (Figure 5)
![Admin Overview](assets/dashboard_admin_overview.png)
- **Top Actions:** 'Export Sales CSV', '+ New Booking', 'Front Desk Console ->'.
- **Salon Operations Quick Hub:** Fast shortcuts to book appointments, create services, add inventory products, and register stylists.
- **Top 4 KPI Metrics:** Today's Revenue, Total Appointments (18 Total / 17 Completed), Registered Users (18 Accounts), Low Stock Alerts (0 Alerts).
- **Revenue Overview Graph:** Interactive daily gross revenue chart (Total: Rs. 54,400 across Aug 22-31).

### 3.2 Enterprise User Management & Role Guard (Figure 6)
![User Management](assets/dashboard_admin_users.png)
Centralized directory of all registered clients, receptionists, and system administrators with role-protection badges and CRUD controls.

### 3.3 Service Catalog CRUD & Dynamic Pricing (Figure 7)
![Salon Services](assets/dashboard_admin_services.png)
Complete control over service packages: Deep Clean Facial (Rs. 2,500), Traditional Shave (Rs. 1,800), Beard Designs (Rs. 2,000), Signature Cut & Beard (Rs. 3,500), and Total Grooming (Rs. 5,000).

### 3.4 Stylists & Master Barbers Commission Management (Figure 8)
![Stylists Management](assets/dashboard_admin_stylists.png)
Automates individual commission rates (Asif: 20%, Sufiyan: 18%, Aliyan: 18%, Zafar: 18%) and work shift schedules (Tue-Sun 11AM-9PM).

## 4. Front Desk Reception Terminal (Floor Operations)
### 4.1 Live Floor & 6-Chair Occupancy Engine (Figure 9)
![Reception Desk](assets/dashboard_reception_floor.png)
- **Live 6-Chair Grid:** Real-time visual tracking of Chair #1 (VIP Master), Chair #2 (Styling), Chair #3 (Grooming), Chair #4 (Executive), Chair #5 (Color Bar), and Chair #6 (Shampoo Basin).
- **In-Chair Elapsed Timers:** Active service timer badge (e.g. ⏱️ In Chair • 10:00 AM).
- **Walk-In Promo Calculator:** Live discount selector (0%, 10%, 15%, 20% Off) with auto-calculated net payable bill.
- **1-Click WhatsApp Customer Alert:** Instant token notification sent to client mobile numbers.
- **80mm Thermal Receipt Slip:** High-contrast isolated print output formatted for counter POS hardware.

## 5. Database Architecture & Entity Relationship Diagram (Figure 10)
![Database ER Diagram](assets/database_er_diagram.png)
The MySQL relational schema utilizes an InnoDB engine with foreign key links ensuring data integrity:
- `user.id` <-> `staff.user_id`: Links system authentication records to barber profiles.
- `user.id` <-> `appointments.user_id`: Tracks client booking history.
- `services.id` <-> `appointments.service_id`: Maps pricing, durations, and revenue reporting.
- `staff.id` <-> `appointments.stylist_id`: Automates master barber 18% commission calculation.
- `inventory`: Manages product stock levels, thresholds, and WhatsApp supplier reorders.
- `reviews`: Tracks customer ratings and testimonial approvals.
- `contact_messages`: Captures inbound customer contact inquiries.

## 6. System User Accounts
| Role | Email Address | Password | Portal |
| :--- | :--- | :--- | :--- |
| **Executive Admin** | `admin@elegance.pk` | `admin123` | `dashboard/admin.php` |
| **Reception Cashier** | `reception@elegance.pk` | `reception123` | `dashboard/reception.php` |
| **Luxury Client** | `client@elegance.pk` | `client123` | `userside/dashboard.php` |

