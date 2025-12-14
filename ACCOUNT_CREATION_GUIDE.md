# Account Creation Methods - Laundry Management System

## Overview
There are two ways customers can create accounts in the system:

---

## 1. MANUAL ACCOUNT CREATION (Admin Creates Account)

### When to Use
- Walk-in customers who want to use the system
- Customers without smartphones or internet access
- Quick registration at the counter

### Process

**Step 1: Admin navigates to Customers**
- Go to Admin Dashboard → Customers → "Add New Customer" button

**Step 2: Admin fills in customer details**
- **Name** (required) - Customer's full name
- **Email** (required) - Customer's email address
- **Phone** (required) - 11-digit phone number (09XXXXXXXXX)
- **Address** - Barangay, Purok, Street, and additional details
- **Notes** - Any special information about the customer

**Step 3: Admin submits the form**
- Click "Create Customer" button
- Confirm in the modal popup

**Step 4: System creates account automatically**
- A User account is created in the system
- A temporary random password is generated (12 characters)
- Customer record is linked to the User account

**Step 5: Admin receives credentials**
- Success page displays:
  - Customer name
  - Customer email
  - **Temporary password** (with copy button)
- Admin shares the password with the customer

**Step 6: Customer logs in**
- Customer uses their email and temporary password to log in
- Customer can then change their password in their profile settings

### Advantages
✓ Fast registration process
✓ No customer needs to remember complex passwords
✓ Admin controls the data entry
✓ Customer can start using the system immediately

---

## 2. CUSTOMER SELF-REGISTRATION (Customer Creates Own Account)

### When to Use
- Customers who prefer to register themselves
- Online customers
- Customers with smartphones/internet access

### Process

**Step 1: Customer visits login/registration page**
- Customer clicks "Register" or "Create Account" link
- Customer fills in their own details

**Step 2: Customer creates their own password**
- Customer chooses their own secure password
- Customer confirms the password

**Step 3: System creates account**
- Account is created with customer's chosen password
- Customer is ready to log in immediately

**Step 4: Customer logs in**
- Uses their email and password they created

### Advantages
✓ Customer has full control of their password
✓ No admin intervention needed
✓ Customer remembers their own password
✓ More secure (customer's choice of password)

---

## Comparison Table

| Feature | Manual (Admin) | Self-Registration (Customer) |
|---------|---|---|
| Who creates account | Admin | Customer |
| Password | System-generated (temporary) | Customer-chosen |
| Time to set up | Immediate | Depends on customer |
| Customer effort | None | Must register themselves |
| Best for | Walk-in customers | Online customers |
| Security | Admin controls data | Customer controls password |
| Password change | Customer can change after login | Already their choice |

---

## Important Notes

### Manual Account Creation
- Email must be unique (no duplicate emails allowed)
- Temporary password is shown only once on success page
- Admin should copy and securely share the password with customer
- Customer can change password after first login

### Customer Self-Registration
- Customer must have internet access
- Customer must remember their password
- Customer must provide accurate information
- Account is created immediately upon registration

---

## Workflow Examples

### Example 1: Walk-in Customer (Manual)
```
Walk-in customer arrives
    ↓
Admin creates account in system
    ↓
Admin provides email & temporary password
    ↓
Customer logs in with provided credentials
    ↓
Customer changes password (optional)
    ↓
Customer can now place orders online
```

### Example 2: Online Customer (Self-Registration)
```
Customer visits website
    ↓
Customer clicks "Register"
    ↓
Customer fills form with their details
    ↓
Customer creates their own password
    ↓
Account is created
    ↓
Customer logs in with their credentials
    ↓
Customer can place orders
```

---

## Current System Status

✅ **Manual Account Creation** - IMPLEMENTED
- Admin can create customer accounts
- Temporary password is generated and displayed
- Customer can log in immediately

⏳ **Customer Self-Registration** - TO BE IMPLEMENTED
- Customers will be able to register themselves
- Choose their own password
- Create account without admin help

