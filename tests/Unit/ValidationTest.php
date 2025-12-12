<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Rules\ValidWeight;
use App\Rules\ValidMonetary;
use App\Rules\ValidFutureDate;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ValidationTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function valid_weight_rule_accepts_valid_weights()
    {
        $rule = new ValidWeight();
        
        // Valid weights
        $this->assertTrue($rule->passes('weight', 1.5));
        $this->assertTrue($rule->passes('weight', 0.1));
        $this->assertTrue($rule->passes('weight', 50));
        $this->assertTrue($rule->passes('weight', 100));
        $this->assertTrue($rule->passes('weight', '25.5'));
    }

    /** @test */
    public function valid_weight_rule_rejects_invalid_weights()
    {
        $rule = new ValidWeight();
        
        // Invalid weights
        $this->assertFalse($rule->passes('weight', 0));
        $this->assertFalse($rule->passes('weight', -1));
        $this->assertFalse($rule->passes('weight', 100.1));
        $this->assertFalse($rule->passes('weight', 'abc'));
        $this->assertFalse($rule->passes('weight', null));
        $this->assertFalse($rule->passes('weight', ''));
    }

    /** @test */
    public function valid_monetary_rule_accepts_valid_amounts()
    {
        $rule = new ValidMonetary();
        
        // Valid amounts
        $this->assertTrue($rule->passes('amount', 0));
        $this->assertTrue($rule->passes('amount', 1.99));
        $this->assertTrue($rule->passes('amount', 99999.99));
        $this->assertTrue($rule->passes('amount', '150.50'));
    }

    /** @test */
    public function valid_monetary_rule_rejects_invalid_amounts()
    {
        $rule = new ValidMonetary();
        
        // Invalid amounts
        $this->assertFalse($rule->passes('amount', -1));
        $this->assertFalse($rule->passes('amount', 100000));
        $this->assertFalse($rule->passes('amount', 'abc'));
        $this->assertFalse($rule->passes('amount', null));
    }

    /** @test */
    public function valid_future_date_rule_accepts_future_dates()
    {
        $rule = new ValidFutureDate(false, 1); // No today, minimum 1 day
        
        // Valid future dates
        $this->assertTrue($rule->passes('date', now()->addDays(2)->toDateString()));
        $this->assertTrue($rule->passes('date', now()->addMonth()->toDateString()));
    }

    /** @test */
    public function valid_future_date_rule_rejects_invalid_dates()
    {
        $rule = new ValidFutureDate(false, 1); // No today, minimum 1 day
        
        // Invalid dates
        $this->assertFalse($rule->passes('date', now()->toDateString()));
        $this->assertFalse($rule->passes('date', now()->subDay()->toDateString()));
        $this->assertFalse($rule->passes('date', 'invalid-date'));
        $this->assertFalse($rule->passes('date', null));
    }

    /** @test */
    public function valid_future_date_rule_allows_today_when_configured()
    {
        $rule = new ValidFutureDate(true, 0); // Allow today, no minimum days
        
        // Should allow today
        $this->assertTrue($rule->passes('date', now()->toDateString()));
        $this->assertTrue($rule->passes('date', now()->addDay()->toDateString()));
    }

    /** @test */
    public function order_creation_validation_works()
    {
        // Test valid order data
        $validData = [
            'customer_id' => 1,
            'weight' => 5.5,
            'subtotal' => 825.00,
            'discount' => 0.00,
            'total_amount' => 825.00,
            'amount_paid' => 825.00,
            'estimated_finish' => now()->addDays(2)->toDateString(),
            'remarks' => 'Test order',
            'priority' => 'normal',
            'service_type' => 'standard',
        ];

        $response = $this->post('/orders', $validData);
        
        // Should pass validation (assuming customer exists)
        $this->assertNotContains('validation error', strtolower($response->getContent()));
    }

    /** @test */
    public function order_creation_rejects_invalid_data()
    {
        // Test invalid order data
        $invalidData = [
            'customer_id' => 999, // Non-existent customer
            'weight' => -5, // Negative weight
            'subtotal' => -100, // Negative subtotal
            'discount' => 200, // Discount greater than subtotal
            'total_amount' => -50, // Negative total
            'amount_paid' => 1000, // Amount paid greater than total
            'estimated_finish' => '2020-01-01', // Past date
            'remarks' => '<script>alert("xss")</script>', // XSS attempt
            'priority' => 'invalid', // Invalid priority
            'service_type' => 'invalid', // Invalid service type
        ];

        $response = $this->post('/orders', $invalidData);
        
        // Should fail validation
        $this->assertContains('validation error', strtolower($response->getContent()));
    }
}
