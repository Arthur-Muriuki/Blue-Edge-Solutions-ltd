# Blue Edge Solutions Limited | IT Infrastructure & E‑Commerce Platform

An enterprise‑grade website and web application for **Blue Edge Solutions Limited**, featuring IT service offerings, a dynamic hardware shop, slide‑out cart drawer, persistent guest session tracking, automated order ticketing, and real‑time administrative order management.

---

##  Key Features

- **Interactive Hardware Shop & Cart Drawer**  
  Instant “Add to Cart” toast notifications with dynamic slide‑out cart drawer calculations and responsive mobile support.

- **Persistent Guest Sessions**  
  Uses 30-day device cookies to automatically remember and restore guest carts across session expirations or browser restarts.

- **Automated Ticket Generation**  
  Converts cart checkouts into unique, trackable order reference codes (e.g., `BES-4A8F21`).

- **Smart Ticket Tracking & Auto-Lookup**  
  Stores the client's latest order reference in a 90-day cookie (`last_ticket_ref`), allowing guests to return to `ticket.php` and view their receipt instantly without typing a code. Includes a manual ticket search fallback.

- **WhatsApp Checkout Integration**  
  Pre‑formats customer WhatsApp lead messages embedded with direct, unique ticket tracking links.

- **Printable Receipts & Client Tickets**  
  Interactive client ticket pages (`ticket.php`) displaying real‑time order status, order item breakdowns, direct WhatsApp support links, and print-ready receipt layouts.

- **GDPR & Privacy Compliance**  
  Built-in cookie consent banner storing user preferences for 365 days, linked to a dedicated Privacy Policy page (`privacy-policy.php`).

- **Dynamic Multi-Brand Header**  
  Contextual branding logic that switches site identity dynamically for specialized landing pages (e.g., **UserCraft**).

- **Staff Admin Dashboard**  
  Secured admin control panel (`admin/index.php`) for live order status toggling (`PENDING`, `PROCESSING`, `COMPLETED`), ticket updates, and product management.

- **Self‑Healing Database Schema**  
  Automatic database table creation (`orders` and `order_items`) and dynamic column patching upon checkout execution.

---

##  Tech Stack

- **Backend:** PHP 8.x (PDO / MySQL, Native Sessions & Cookies)  
- **Frontend:** HTML5, CSS3, Vanilla JavaScript (ES6+), Phosphor Icons  
- **Database:** MySQL / MariaDB  

---

##  Important Notes

- This application is in **active development** — features and schemas may be updated.  
- Intended for **authorized internal use** and official company clients.  

---

##  License & Usage

This software and its source code are the proprietary property of **Blue Edge Solutions Limited**.  
It is intended solely for internal use by authorized personnel or clients of the company.  

Unauthorized copying, distribution, modification, or deployment of this software, in whole or in part, is strictly prohibited.  

For inquiries, contact: **muriukicaleb6@gmail.com**