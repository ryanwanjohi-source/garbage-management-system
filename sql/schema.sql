-- ============================================================
--  Smart Sweep — database schema and starting data
--  Database: smart_sweep (MySQL, run under XAMPP)
--
--  HOW TO SET UP:
--  1. Start Apache and MySQL in the XAMPP Control Panel.
--  2. Open phpMyAdmin (http://localhost/phpmyadmin), go to
--     the "Import" tab and choose this file — OR run it from
--     the command line:
--       C:\xampp\mysql\bin\mysql.exe -u root < schema.sql
--  3. The site's pages connect with includes/db.php.
--
--  ADMIN LOGIN (for login.php):
--     username: admin
--     password: admin123
--  The password is stored as a bcrypt hash, never in plain
--  text. Change it later by generating a new hash with
--  password_hash() and updating the admins table.
-- ============================================================

CREATE DATABASE IF NOT EXISTS smart_sweep CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE smart_sweep;
SET NAMES utf8mb4;

-- ------------------------------------------------------------
--  admins — the only people who can log in to the backend.
-- ------------------------------------------------------------
DROP TABLE IF EXISTS admins;
CREATE TABLE admins (
    id            INT AUTO_INCREMENT PRIMARY KEY,
    full_name     VARCHAR(100) NOT NULL,
    username      VARCHAR(50)  NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    created_at    TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

INSERT INTO admins (full_name, username, password_hash) VALUES
('Ryan Wachira', 'admin', '$2y$10$ALBaMgkiOco6wOtfeNajjuchCwuzvvE08l/d8GEyBkuxJYmwDH4/W');

-- ------------------------------------------------------------
--  bins — every smart bin. fill_level and weight_kg are NULL
--  while a bin is offline (it has sent no reading).
-- ------------------------------------------------------------
DROP TABLE IF EXISTS bins;
CREATE TABLE bins (
    id           INT AUTO_INCREMENT PRIMARY KEY,
    bin_code     VARCHAR(10)  NOT NULL UNIQUE,
    location     VARCHAR(120) NOT NULL,
    waste_type   VARCHAR(50)  NOT NULL,
    fill_level   INT          NULL,
    weight_kg    DECIMAL(6,1) NULL,
    last_reading VARCHAR(20)  NOT NULL,
    online       TINYINT(1)   NOT NULL DEFAULT 1
);

INSERT INTO bins (bin_code, location, waste_type, fill_level, weight_kg, last_reading, online) VALUES
('NRB-001', 'Kilimani, Yaya Centre',       'General',         92, 148.2, '08:42',    1),
('NRB-002', 'Westlands, Sarit Centre',     'Dry Recyclables', 74,  96.5, '08:37',    1),
('NRB-003', 'Karen, Hardy',                'Wet Organic',     41,  58.9, '08:31',    1),
('NRB-004', 'Ruaka, Two Rivers',           'Metallic',        85, 132.7, '07:15',    1),
('NRB-005', 'Embakasi, Fedha Estate',      'General',         23,  31.4, '08:44',    1),
('NRB-006', 'Kasarani, TRM Drive',         'Dry Recyclables', 68,  88.1, '08:29',    1),
('NRB-007', 'South C, Mugoya Estate',      'Wet Organic',     37,  49.6, '08:40',    1),
('NRB-008', 'Lavington, James Gichuru Rd', 'General',         79, 117.3, '08:12',    1),
('NRB-009', 'Kileleshwa, Oloitokitok Rd',  'Dry Recyclables', 54,  70.8, '08:26',    1),
('NRB-010', 'Ngong Road, Prestige Plaza',  'General',         12,  15.2, '08:45',    1),
('NRB-011', 'Ruaraka, Garden Estate',      'Metallic',        NULL, NULL, 'Yesterday', 0),
('NRB-012', 'Upper Hill, Community Area',  'Wet Organic',     NULL, NULL, 'Yesterday', 0);

-- ------------------------------------------------------------
--  alerts — raised when a bin fills up, a driver reports a
--  delay, or a sensor goes offline. "message" and
--  "message_sub" are the two lines shown in the dashboard's
--  Latest Alerts feed.
-- ------------------------------------------------------------
DROP TABLE IF EXISTS alerts;
CREATE TABLE alerts (
    id           INT AUTO_INCREMENT PRIMARY KEY,
    alert_code   VARCHAR(10)  NOT NULL UNIQUE,
    subject      VARCHAR(120) NOT NULL,
    alert_type   VARCHAR(30)  NOT NULL,
    detail       VARCHAR(20)  NOT NULL DEFAULT '',
    raised       VARCHAR(30)  NOT NULL,
    acknowledged TINYINT(1)   NOT NULL DEFAULT 0,
    note         VARCHAR(120) NULL,
    message      VARCHAR(160) NOT NULL,
    message_sub  VARCHAR(120) NOT NULL
);

INSERT INTO alerts (alert_code, subject, alert_type, detail, raised, acknowledged, note, message, message_sub) VALUES
('AL-1042', 'NRB-001 — Kilimani',    'Fill Level',    '92%',   'Today, 08:42',     0, NULL,                'NRB-001 reached 92% — fill-level alert', 'Kilimani · Today, 08:42'),
('AL-1041', 'NRB-004 — Ruaka',       'Fill Level',    '85%',   'Today, 07:15',     0, NULL,                'NRB-004 reached 85% — fill-level alert', 'Ruaka · Today, 07:15'),
('AL-1040', 'Route R-03 — Kasarani', 'Delay',         '25 min','Today, 09:03',     0, NULL,                'Route R-03 delayed by 25 minutes',       'Driver J. Mwangi · Today, 09:03'),
('AL-1039', 'NRB-008 — Lavington',   'Fill Level',    '79%',   'Today, 08:12',     1, 'Route R-05',        'NRB-008 reached 79% — fill-level alert', 'Lavington · Today, 08:12'),
('AL-1038', 'NRB-002 — Westlands',   'Fill Level',    '74%',   'Today, 06:58',     1, 'Route R-02',        'NRB-002 reached 74% — fill-level alert', 'Westlands · Today, 06:58'),
('AL-1037', 'NRB-011 — Ruaraka',     'Sensor Offline','',      'Yesterday, 18:20', 1, 'Technician assigned','NRB-011 sensor went offline',           'Ruaraka · Yesterday, 18:20');

-- ------------------------------------------------------------
--  routes and route_stops — one route is one truck trip;
--  its stops are the bins it empties, in order.
-- ------------------------------------------------------------
DROP TABLE IF EXISTS route_stops;
DROP TABLE IF EXISTS routes;
CREATE TABLE routes (
    id        INT AUTO_INCREMENT PRIMARY KEY,
    route_code VARCHAR(10)  NOT NULL UNIQUE,
    name      VARCHAR(120) NOT NULL,
    driver    VARCHAR(60)  NOT NULL,
    vehicle   VARCHAR(20)  NOT NULL,
    status    VARCHAR(20)  NOT NULL,
    details   VARCHAR(200) NOT NULL
);

CREATE TABLE route_stops (
    id         INT AUTO_INCREMENT PRIMARY KEY,
    route_id   INT NOT NULL,
    stop_order INT NOT NULL,
    label      VARCHAR(120) NOT NULL,
    FOREIGN KEY (route_id) REFERENCES routes(id)
);

INSERT INTO routes (route_code, name, driver, vehicle, status, details) VALUES
('R-05', 'Kilimani & Ruaka Pickups', 'P. Otieno',  'KDL 452B', 'Planned',     'Starts 10:30 · Covers the two full bins from today''s alerts'),
('R-03', 'Kasarani Round',           'J. Mwangi',  'KCX 219A', 'In Progress', 'Started 07:40 · Delayed 25 min (reported by the driver)'),
('R-02', 'Westlands Round',          'M. Njoroge', 'KDL 907C', 'Completed',   'Finished 08:20 · 6 bins emptied'),
('R-01', 'Karen & South C Round',    'P. Otieno',  'KDL 452B', 'Completed',   'Finished 07:55 · 5 bins emptied');

INSERT INTO route_stops (route_id, stop_order, label) VALUES
(1, 1, 'Depot'), (1, 2, 'NRB-001 Kilimani (92%)'), (1, 3, 'NRB-004 Ruaka (85%)'), (1, 4, 'NRB-009 Kileleshwa (54%)'), (1, 5, 'Dandora Dumpsite'),
(2, 1, 'Depot'), (2, 2, 'NRB-006 Kasarani (68%)'), (2, 3, 'NRB-011 Ruaraka (offline)'), (2, 4, 'Dandora Dumpsite'),
(3, 1, 'Depot'), (3, 2, 'NRB-002 Westlands (74%)'), (3, 3, 'NRB-008 Lavington (79%)'), (3, 4, 'Dandora Dumpsite'),
(4, 1, 'Depot'), (4, 2, 'NRB-003 Karen (41%)'), (4, 3, 'NRB-007 South C (37%)'), (4, 4, 'NRB-005 Embakasi (23%)'), (4, 5, 'Dandora Dumpsite');

-- ------------------------------------------------------------
--  users — everyone in the system directory: staff and
--  residents. New residents are added by signup.php, with
--  their password stored as a hash. (Only the admins table
--  can log in to this backend.)
-- ------------------------------------------------------------
DROP TABLE IF EXISTS users;
CREATE TABLE users (
    id            INT AUTO_INCREMENT PRIMARY KEY,
    full_name     VARCHAR(100) NOT NULL,
    username      VARCHAR(50)  NOT NULL UNIQUE,
    role          VARCHAR(30)  NOT NULL,
    phone         VARCHAR(20)  NOT NULL,
    email         VARCHAR(120) NULL,
    estate        VARCHAR(80)  NULL,
    password_hash VARCHAR(255) NULL,
    status        VARCHAR(30)  NOT NULL DEFAULT 'Active',
    created_at    TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

INSERT INTO users (full_name, username, role, phone, status) VALUES
('Ryan Wachira',  'admin',   'Administrator',     '0712 345 678', 'Active'),
('David Kamau',   'dkamau',  'Dispatcher',        '0733 218 540', 'Active'),
('James Mwangi',  'jmwangi', 'Collection Driver', '0701 884 236', 'Active'),
('Ann Wambui',    'awambui', 'Accountant',        '0722 450 917', 'Active'),
('Peter Otieno',  'potieno', 'Collection Driver', '0740 663 128', 'Active'),
('Grace Achieng', 'gachieng','Resident',          '0715 902 344', 'Active'),
('Faith Njeri',   'fnjeri',  'Resident',          '0768 117 205', 'Unpaid Invoice'),
('John Kiprop',   'jkiprop', 'Resident',          '0790 554 731', 'Active');

-- ------------------------------------------------------------
--  invoices and payments — the money side, shown on the
--  Reports page.
-- ------------------------------------------------------------
DROP TABLE IF EXISTS payments;
DROP TABLE IF EXISTS invoices;
CREATE TABLE invoices (
    id           INT AUTO_INCREMENT PRIMARY KEY,
    invoice_code VARCHAR(10) NOT NULL UNIQUE,
    resident_name VARCHAR(100) NOT NULL,
    estate       VARCHAR(80)  NOT NULL,
    amount       INT          NOT NULL,
    status       VARCHAR(10)  NOT NULL
);

CREATE TABLE payments (
    id           INT AUTO_INCREMENT PRIMARY KEY,
    payment_code VARCHAR(10) NOT NULL UNIQUE,
    invoice_code VARCHAR(10) NOT NULL,
    method       VARCHAR(20) NOT NULL,
    mpesa_ref    VARCHAR(20) NULL,
    pay_date     VARCHAR(20) NOT NULL,
    amount       INT         NOT NULL
);

INSERT INTO invoices (invoice_code, resident_name, estate, amount, status) VALUES
('INV-2214', 'Grace Achieng',   'Kilimani',  1500, 'Paid'),
('INV-2213', 'Peter Maina',     'Westlands', 1500, 'Paid'),
('INV-2212', 'Faith Njeri',     'Ruaka',     2000, 'Unpaid'),
('INV-2211', 'John Kiprop',     'Karen',     2500, 'Paid'),
('INV-2210', 'Mary Wanjiku',    'South C',   1500, 'Unpaid'),
('INV-2209', 'Samuel Odhiambo', 'Kasarani',  1200, 'Paid');

INSERT INTO payments (payment_code, invoice_code, method, mpesa_ref, pay_date, amount) VALUES
('PAY-0871', 'INV-2214', 'M-Pesa', 'QK72HD81PX', '8 Nov 2026', 1500),
('PAY-0870', 'INV-2213', 'M-Pesa', 'QK71MB30RD', '8 Nov 2026', 1500),
('PAY-0869', 'INV-2211', 'M-Pesa', 'QK70ZT55NK', '7 Nov 2026', 2500),
('PAY-0868', 'INV-2209', 'Card',   NULL,         '7 Nov 2026', 1200);
