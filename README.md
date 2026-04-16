# DNSC Water Refilling Ordering System (Web-Based MVP)

## 🚀 How to Run the System

### Prerequisites
1.  **XAMPP** must be installed and running.
2.  Start **Apache** and **MySQL** modules in XAMPP Control Panel.

### Installation (First Time Only)
1.  Open Command Prompt (Terminal) in this folder: `c:\xampp\htdocs\Water Refilling`.
2.  Install Dependencies:
    ```bash
    composer install
    ```
3.  Setup Environment:
    - Copy `.env.example` to `.env`.
    - Update `.env`: `DB_DATABASE=water_refilling`.
4.  Generate Key:
    ```bash
    php artisan key:generate
    ```
5.  Setup Database:
    ```bash
    php artisan migrate:fresh --seed
    ```
    *(Note: This resets the database and creates the default admin accounts).*

### ▶️ Running the Application
1.  Open Command Prompt in the project folder.
2.  Run the server:
    ```bash
    php artisan serve
    ```
3.  Open your browser and visit: [http://127.0.0.1:8000](http://127.0.0.1:8000)

---

## 🔑 Login Credentials

### Manager Account (Sir K)
- **Email:** `manager@dnsc.edu.ph`
- **Password:** `password`
- **Access:** Dashboard, Approve/Reject Orders, Print Reports, Walk-in Entry.

### Staff Accounts (Sir James / Sir Cha)
- **Email:** `james@dnsc.edu.ph` / `cha@dnsc.edu.ph`
- **Password:** `password`
- **Access:** Dashboard, Update Order Status, Print Reports.

---

## 📅 Business Rules implemented
1.  **Guest Ordering:**
    -   Clients can place orders **24/7**.
    -   **Delivery Date** selection is restricted to **Tuesdays & Fridays** only.
    -   Price is auto-calculated at **₱25.00** per container.

2.  **Order Processing:**
    -   Orders start as **Pending**.
    -   Admin must **Confirm** -> **Complete** orders.
    -   Walk-in orders are automatically marked as **Completed**.

3.  **Reporting:**
    -   **Billing Statement:** Individual printable invoice for each order.
    -   **Financial Report:** Summary of sales and volume.
