# Laundry Management System - Demonstration Script

## Project Overview
A comprehensive Laravel-based laundry management system with admin dashboard, order management, capacity planning, and backlog handling.

---

## 1. ADMIN DASHBOARD WALKTHROUGH

### 1.1 Access the Dashboard
**Path:** `/admin/dashboard`

**What to Show:**
- **Capacity Overview Card**
  - Washers utilization percentage and load (kg)
  - Dryers utilization percentage and load (kg)
  - Today's Load total weight
  - Confirmed Kilos (orders with confirmed weight)
  - Backlog weight (if any)
  - Status badge: "Normal" or "Backlog"

- **Backlog Orders Card** (Will Wash Tomorrow)
  - List of orders exceeding today's capacity
  - Order ID, Customer Name, Weight, Service Type, Estimated Time
  - Total backlog count
  - "View All Backlog Orders" button

- **Orders Today Card**
  - Orders created today with status
  - Order count breakdown: Completed, Processing, Pending
  - Real-time status updates

### 1.2 Backlog Notification Alert
**Scenario:** When an order exceeds daily capacity
- Orange alert banner appears at top of dashboard
- Shows: Order ID, Customer Name, Weight, Message
- Indicates order has been automatically moved to backlog

---

## 2. ORDER MANAGEMENT

### 2.1 Create New Order
**Path:** `/admin/orders/create`

**Steps:**
1. Select customer from dropdown (or create new virtual customer)
2. Enter order details:
   - Weight (kg)
   - Service type
   - Detergent quantity (optional)
   - Fabric conditioner quantity (optional)
   - Subtotal and total amount
   - Amount paid
   - Pickup date (optional)
   - Estimated finish date
   - Remarks (optional)
3. Click "Create Order"

**Expected Behavior:**
- Order created successfully message appears
- If order exceeds capacity: "This order has been placed in backlog as it exceeds today's capacity."
- Order appears in orders list

### 2.2 View All Orders
**Path:** `/admin/orders`

**Features:**
- **Filters:**
  - Status filter (Pending, Approved, Washing, Drying, Folding, Quality Check, Ready, Completed, Cancelled)
  - Source filter (Walk-in, Online)
  - Backlog filter (All Orders, Backlog Only - Will Wash Tomorrow)

- **Order Table Columns:**
  - Order ID (3-digit padded)
  - Customer Name
  - Order Type (service_type)
  - Kilos (confirmed weight or estimated weight)
  - Status (color-coded badge)
  - Total Amount (₱)
  - Payment Status (Fully Paid, Partially Paid, Unpaid)
  - Pickup Date
  - Actions (View, Edit, Delete)

### 2.3 Backlog Filter Demonstration
**Steps:**
1. Go to Orders page
2. Select "Backlog Only (Will Wash Tomorrow)" from Backlog Filter dropdown
3. Page displays only orders that exceed daily capacity
4. These orders will be washed tomorrow, not today

**Key Information:**
- Daily Washer Capacity: 480kg (5 washers × 12 hours × 8kg per cycle)
- Orders are sequentially evaluated
- First order to exceed capacity and all subsequent orders go to backlog

---

## 3. CAPACITY CALCULATION LOGIC

### 3.1 How Backlog is Determined
**Algorithm:**
1. Fetch all orders created today, sorted by creation time (oldest first)
2. Iterate through orders, accumulating weight
3. When cumulative weight exceeds 480kg:
   - That order and all subsequent orders are marked as backlog
   - These orders will be washed tomorrow

**Example:**
```
Order 1: 100kg → Cumulative: 100kg (within capacity)
Order 2: 150kg → Cumulative: 250kg (within capacity)
Order 3: 200kg → Cumulative: 450kg (within capacity)
Order 4: 50kg  → Cumulative: 500kg (EXCEEDS 480kg) ⚠️ BACKLOG
Order 5: 80kg  → Cumulative: 580kg (BACKLOG)
```

### 3.2 Confirmed Kilos
- Displayed in Capacity Overview
- Includes orders with `confirmed_weight` set
- Also includes orders with status "approved"
- Used for accurate capacity utilization calculations

---

## 4. REPORTS PAGE

### 4.1 Access Reports
**Path:** `/admin/reports`

**Features:**
- **Date Range Filtering**
  - Start date and end date pickers
  - Default: Last 30 days

- **Order Filters**
  - Order Status filter
  - Payment Status filter (Fully Paid, Partially Paid, Unpaid)

- **Summary Statistics**
  - Total Orders (count)
  - Total Revenue (₱)
  - Average Order Value (₱)
  - Completion Rate (%)

- **Monthly Sales Breakdown**
  - Table showing orders and revenue by month
  - Helps track sales trends

- **Recent Orders Table**
  - Filtered orders with all details
  - Pagination (15 per page)

---

## 5. CUSTOMER MANAGEMENT

### 5.1 Customer Address Details
**Stored Fields:**
- Name
- Email
- Phone
- Address (optional)
- Barangay (optional)
- Purok (optional)
- Street (optional)

**Note:** Address details are optional during order creation. If provided, they are saved to the customer record.

### 5.2 Customer Types
- **Online Customers:** Have user accounts, place orders through user portal
- **Walk-in Customers:** Created by admin, no user account

---

## 6. PAYMENT STATUS LOGIC

### 6.1 Payment Status Determination
```
Fully Paid:     amount_paid >= total_amount
Partially Paid: amount_paid > 0 AND amount_paid < total_amount
Unpaid:         amount_paid == 0
```

### 6.2 Payment Tracking
- Displayed in orders table
- Filterable in reports page
- Shows payment progress for each order

---

## 7. DEMONSTRATION SCENARIOS

### Scenario 1: Normal Day (No Backlog)
**Steps:**
1. Create orders totaling 300kg
2. Check dashboard → "Normal" status badge
3. All orders appear in "Orders Today" card
4. No backlog notification appears

**Expected Result:**
- Capacity Overview shows: 300kg / 480kg
- Utilization: ~62%
- No backlog orders

### Scenario 2: Capacity Exceeded (Backlog Triggered)
**Steps:**
1. Create orders:
   - Order 1: 150kg
   - Order 2: 200kg
   - Order 3: 100kg (this exceeds capacity)
2. Check dashboard

**Expected Result:**
- Orange alert banner appears
- Shows Order 3 details
- Message: "Order #003 has been placed in backlog as it exceeds today's capacity."
- Backlog Orders card shows Order 3
- Capacity Overview shows "Backlog" status badge
- Backlog weight: 100kg

### Scenario 3: Filtering Backlog Orders
**Steps:**
1. Go to Orders page
2. Select "Backlog Only (Will Wash Tomorrow)" filter
3. View filtered results

**Expected Result:**
- Only backlog orders display
- Can see all orders that will be washed tomorrow
- Can still apply status and source filters alongside backlog filter

### Scenario 4: Confirmed Weight vs Estimated Weight
**Steps:**
1. Create order with estimated weight: 50kg
2. In orders table, weight shows as "50kg" (gray)
3. Admin confirms actual weight: 45kg
4. In orders table, weight shows as "45kg (confirmed)" (blue)

**Expected Result:**
- Confirmed weight takes precedence in capacity calculations
- Capacity Overview updates with confirmed kilos
- Utilization percentages recalculate

---

## 8. KEY FEATURES SUMMARY

### ✅ Implemented Features
- [x] Admin dashboard with real-time capacity overview
- [x] Order creation with validation
- [x] Automatic backlog detection and notification
- [x] Backlog filtering in orders list
- [x] Confirmed kilos tracking
- [x] Payment status tracking
- [x] Reports page with date range and status filtering
- [x] Monthly sales breakdown
- [x] Customer address details (optional)
- [x] Order success messages with backlog status
- [x] Responsive design (mobile, tablet, desktop)

### 📊 Capacity Management
- Daily capacity: 480kg (5 washers × 12 hours × 8kg/cycle)
- Sequential backlog allocation
- Real-time utilization percentage
- Backlog weight tracking

### 🔔 Notifications
- Backlog alert on dashboard
- Order creation success message
- Status change notifications (via notification system)

---

## 9. TECHNICAL STACK

- **Framework:** Laravel 10+
- **Frontend:** Blade templates with Tailwind CSS
- **Database:** MySQL/PostgreSQL
- **Caching:** Laravel Cache (for order statistics)
- **ORM:** Eloquent

---

## 10. USER ROLES

### Admin
- Full access to dashboard
- Create/edit/delete orders
- View reports
- Manage capacity
- See backlog notifications

### Regular User (Online Customer)
- View own orders
- Create orders
- Track order status

---

## 11. TESTING CHECKLIST

- [ ] Create order and verify success message
- [ ] Create order exceeding capacity and verify backlog notification
- [ ] Filter orders by backlog status
- [ ] Verify confirmed kilos in capacity overview
- [ ] Check payment status filtering in reports
- [ ] Verify monthly sales breakdown
- [ ] Test responsive design on mobile
- [ ] Verify order table displays kilos column correctly
- [ ] Test date range filtering in reports
- [ ] Verify backlog orders show correct weight and customer info

---

## 12. NAVIGATION QUICK LINKS

| Feature | Path | Role |
|---------|------|------|
| Dashboard | `/admin/dashboard` | Admin |
| Create Order | `/admin/orders/create` | Admin |
| View Orders | `/admin/orders` | Admin |
| View Reports | `/admin/reports` | Admin |
| My Orders | `/orders` | User |
| Create Order | `/orders/create` | User |

---

## 13. COMMON WORKFLOWS

### Workflow 1: Daily Order Processing
1. Admin logs in → Dashboard
2. Reviews capacity overview
3. Checks backlog orders (if any)
4. Creates new orders as they come in
5. System automatically handles backlog
6. Admin reviews reports at end of day

### Workflow 2: Capacity Planning
1. Admin checks "Backlog Orders" card
2. Clicks "View All Backlog Orders"
3. Sees all orders for tomorrow
4. Plans washing schedule accordingly
5. Adjusts staffing if needed

### Workflow 3: Payment Tracking
1. Admin goes to Reports page
2. Filters by payment status
3. Identifies unpaid orders
4. Follows up with customers
5. Updates payment status

---

## 14. EDGE CASES HANDLED

- **No backlog orders:** Message displays "No backlog orders for tomorrow"
- **Multiple orders on same day:** Sequential calculation handles all
- **Confirmed weight changes:** Capacity recalculates automatically
- **Approved orders:** Included in confirmed kilos calculation
- **Optional address fields:** System handles null values gracefully
- **Pagination:** Manual pagination for filtered collections

---

## 15. PERFORMANCE OPTIMIZATIONS

- Cached order statistics (pending, in progress, completed counts)
- Selective eager loading (only load necessary relationships)
- Efficient backlog calculation (single pass through orders)
- Pagination to limit data transfer

---

## 16. UI SIDEMENU STRUCTURE

### 16.1 Admin Sidebar
**Location:** `resources/views/components/sidebar-admin.blade.php`

**Features:**
- **Responsive Design**
  - Desktop: Fixed sidebar on left (272px width)
  - Mobile: Collapsible sidebar with overlay
  - Toggle button in mobile header

- **Sidebar Header**
  - Application logo and name
  - Mobile close button
  - Branding area

- **Navigation Menu Items**
  1. **Overview** (Dashboard)
     - Icon: Home icon
     - Route: `admin.dashboard`
     - Shows capacity overview and backlog

  2. **Customers**
     - Icon: People icon
     - Route: `admin.customers.index`
     - Manage customer records

  3. **Laundry Management** (Orders)
     - Icon: Laundry basket icon
     - Route: `admin.orders.index`
     - Create, view, filter orders

  4. **Machines**
     - Icon: Machine icon
     - Route: `machines.dashboard`
     - Manage washing machines and dryers

  5. **Reports**
     - Icon: Report/chart icon
     - Route: `/reports`
     - View sales reports and analytics

- **Active State Styling**
  - Blue background highlight for current page
  - Blue left border indicator
  - Bold text for active menu item
  - Icon color changes to blue

- **Hover Effects**
  - Light gray background on hover
  - Smooth transitions
  - Icon color changes on hover

### 16.2 Desktop Header
**Location:** Top right of page (hidden on mobile)

**Components:**
- **Teams Dropdown** (if enabled)
  - Switch between teams
  - Team management options
  - Create new team

- **Notification Bell**
  - Real-time notifications
  - Shows unread count
  - Click to view notifications

- **User Profile Dropdown**
  - User name or profile photo
  - Profile management
  - API tokens (if enabled)
  - Logout button

### 16.3 Mobile Header
**Location:** Top of page (visible only on mobile)

**Components:**
- **Left: Hamburger Menu Button**
  - Opens/closes sidebar
  - Three-line menu icon

- **Center: Logo**
  - Application logo
  - Mobile branding

- **Right: Notification & Profile**
  - Notification bell
  - User profile dropdown

### 16.4 Sidebar Animations
- **Mobile Sidebar Transition**
  - Slide in from left: 300ms duration
  - Overlay fade effect
  - Smooth transform animation

- **Hover Animations**
  - Background color transitions
  - Icon color changes
  - Smooth 200ms transitions

### 16.5 Color Scheme
- **Background:** White (#FFFFFF)
- **Text (inactive):** Gray-700 (#374151)
- **Text (active):** Blue-700 (#1D4ED8)
- **Background (active):** Blue-50 (#EFF6FF)
- **Border (active):** Blue-700 (#1D4ED8)
- **Hover Background:** Gray-50 (#F9FAFB)
- **Icons (inactive):** Gray-400 (#9CA3AF)
- **Icons (active):** Blue-500 (#3B82F6)
- **Overlay (mobile):** Gray-600 with 75% opacity

### 16.6 Responsive Breakpoints
- **Mobile:** < 1024px (lg breakpoint)
  - Hamburger menu visible
  - Sidebar slides from left
  - Full-width overlay
  - Compact header

- **Desktop:** ≥ 1024px
  - Fixed sidebar always visible
  - Full header with profile dropdown
  - No hamburger menu
  - Sidebar doesn't slide

### 16.7 Navigation Flow
```
Admin Dashboard
├── Overview (Dashboard)
│   └── Capacity Overview
│   └── Backlog Orders
│   └── Orders Today
│
├── Customers
│   └── Customer List
│   └── Add Customer
│
├── Laundry Management (Orders)
│   └── All Orders
│   └── Create Order
│   └── Filter by Status/Source/Backlog
│
├── Machines
│   └── Machine Dashboard
│   └── Washer Management
│   └── Dryer Management
│
└── Reports
    └── Sales Reports
    └── Date Range Filtering
    └── Monthly Breakdown
```

### 16.8 Accessibility Features
- Semantic HTML structure
- ARIA labels for icons
- Keyboard navigation support
- Focus states for interactive elements
- High contrast colors for readability
- Responsive touch targets on mobile

### 16.9 User Sidebar
**Location:** `resources/views/components/sidebar-user.blade.php`

**Features:**
- Similar structure to admin sidebar
- Limited menu items (user-specific)
- Access to personal orders only
- No admin functions visible

---

## 17. CUSTOMER BACKLOG NOTIFICATIONS

### 17.1 Overview
When an online customer's order is placed in backlog (due to exceeding daily capacity), they receive an automatic notification informing them that their order will be washed tomorrow instead of today.

### 17.2 Notification Triggers
**Scenario 1: Order Created with Known Weight**
- **When:** Admin creates order with weight specified
- **Who:** Online customers (customers with user accounts)
- **How:** Automatic notification via `NotificationService::orderPlacedInBacklog()` in `OrderController::store()`

**Scenario 2: Order Measured at Shop**
- **When:** Customer creates order with "measure at shop" option, then admin confirms weight
- **Who:** Online customers (customers with user accounts)
- **How:** Automatic notification via `NotificationService::orderPlacedInBacklog()` in `OrderController::confirmWeight()`
- **Important:** This ensures customers are notified even when weight is confirmed later

### 17.3 Notification Details

**Notification Type:** `order_backlog`

**Title:** `Order #{order_id} Placed in Backlog`

**Message:** 
```
Your laundry order #{order_id} ({weight}kg) has been placed in backlog. 
Due to today's high volume, it will be washed tomorrow instead. 
We appreciate your patience!
```

**Data Included:**
- `order_id` - Order ID
- `weight` - Order weight in kg
- `customer_name` - Customer name
- `estimated_finish` - Estimated completion date/time
- `url` - Link to order details page

### 17.4 Implementation Details

**Service Method:**
```php
NotificationService::orderPlacedInBacklog(Order $order)
```

**Location:** `app/Services/NotificationService.php`

**Conditions:**
- Order must have a customer
- Customer must have a user account (online customer)
- Order must be placed in backlog

**Integration Point:**
- Called in `OrderController::store()` method
- Triggered after order creation and backlog determination
- Executes only if `$isInBacklog === true`

### 17.5 Customer Experience Flow

**Step 1: Customer Creates Order**
- Customer submits order through user portal
- Order is created in system

**Step 2: System Checks Capacity**
- System calculates if order exceeds daily capacity
- If capacity exceeded → Order goes to backlog

**Step 3: Notification Sent**
- If backlog → Notification created for customer
- Notification appears in customer's notification bell
- Customer receives notification alert

**Step 4: Customer Views Notification**
- Customer sees notification in notification bell (top right)
- Notification shows:
  - Order ID
  - Weight
  - Backlog message
  - Link to order details

**Step 5: Customer Takes Action**
- Customer can click notification to view order details
- Customer sees estimated completion time
- Customer understands order will be washed tomorrow

### 17.6 Notification Display Locations

**1. Notification Bell (Top Right)**
- Shows unread notification count
- Click to open notification dropdown
- Displays recent notifications

**2. User Orders Page**
- May show backlog status badge on order
- Indicates order is in backlog

**3. Order Details Page**
- Shows full order information
- Displays backlog status
- Shows estimated completion time

### 17.7 Notification Lifecycle

**Created:** When order is placed in backlog
**Status:** Unread (until customer views)
**Read:** When customer clicks notification or views order
**Marked as Read:** Via notification bell interface

### 17.8 Database Storage

**Table:** `notifications`

**Fields:**
- `type` = 'order_backlog'
- `title` = Order backlog notification title
- `message` = Customer-friendly message
- `notifiable_type` = 'App\Models\User'
- `notifiable_id` = Customer's user ID
- `data` = JSON with order details
- `is_read` = false (initially)
- `read_at` = null (until read)

### 17.9 Notification Types Summary

| Type | Recipient | Trigger | Message |
|------|-----------|---------|---------|
| `order_backlog` | Online Customer | Order exceeds capacity | Order placed in backlog, will wash tomorrow |
| `order_status` | Online Customer | Status changes | Status-specific message |
| `pickup_reminder` | Online Customer | Order ready | Laundry ready for pickup |
| `new_order` | Admin | Order created | New order received |
| `capacity_alert` | Admin | Capacity ≥80% | Capacity warning |

### 17.10 Code Example: Sending Backlog Notification

```php
// In OrderController::store()
if ($isInBacklog) {
    NotificationService::orderPlacedInBacklog($order);
}
```

**Service Implementation:**
```php
public static function orderPlacedInBacklog(Order $order): void
{
    if ($order->customer && $order->customer->user) {
        self::create(
            $order->customer->user,
            'order_backlog',
            "Order #{$order->id} Placed in Backlog",
            "Your laundry order #{$order->id} ({$order->weight}kg) has been placed in backlog. 
             Due to today's high volume, it will be washed tomorrow instead. 
             We appreciate your patience!",
            [
                'order_id' => $order->id,
                'weight' => $order->weight,
                'customer_name' => $order->customer->name,
                'estimated_finish' => $order->estimated_finish?->format('M d, Y g:i A'),
                'url' => route('user.orders.show', $order->id)
            ]
        );
    }
}
```

### 17.11 Walk-in Customer Handling

**Walk-in Customers:** Do NOT receive backlog notifications
- Walk-in customers have no user account
- Notifications are only for online customers
- Admin can inform walk-in customers manually if needed

### 17.12 Best Practices

1. **Clear Messaging:** Message explains why order is in backlog
2. **Transparency:** Customer knows order will be washed tomorrow
3. **Actionable:** Provides link to order details
4. **Timely:** Notification sent immediately when order created
5. **Non-intrusive:** Notification in bell, not popup alert
6. **Appreciative:** Thanks customer for patience

### 17.13 Future Enhancements

- SMS notification option
- Email notification option
- Push notifications for mobile app
- Estimated completion time update when order moves from backlog to processing
- Backlog position indicator (e.g., "You are 3rd in backlog")

---

**Last Updated:** December 14, 2025
**Version:** 1.0
