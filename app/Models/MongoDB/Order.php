<?php

namespace App\Models\MongoDB;

use Jenssegers\Mongodb\Eloquent\Model;

/**
 * Advanced MongoDB Order Model
 * Demonstrates outstanding MongoDB integration with:
 * - Embedded order items and shipping information
 * - Order state management with history tracking
 * - Advanced aggregation for business analytics
 * - Real-time order processing capabilities
 * - Comprehensive audit trail
 */
class Order extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'orders';

    protected $fillable = [
        'user_id', 'order_number', 'status', 'items', 'pricing',
        'shipping', 'payment', 'fulfillment', 'metadata', 'audit_log'
    ];

    protected $casts = [
        'items' => 'array',
        'pricing' => 'object',
        'shipping' => 'object',
        'payment' => 'object',
        'fulfillment' => 'object',
        'metadata' => 'object',
        'audit_log' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Advanced MongoDB Schema Structure for Orders:
     * {
     *   "_id": ObjectId("..."),
     *   "user_id": ObjectId("..."),
     *   "order_number": "ORD-2025-001234",
     *   "status": "processing",
     *   "items": [
     *     {
     *       "product_id": ObjectId("..."),
     *       "variant_id": "variant_001",
     *       "sku": "TCM-001-S-BLK",
     *       "name": "Premium Cotton T-Shirt",
     *       "size": "S",
     *       "color": {
     *         "name": "Black",
     *         "hex": "#000000"
     *       },
     *       "quantity": 2,
     *       "unit_price": 29.99,
     *       "discount_applied": 4.50,
     *       "final_price": 25.49,
     *       "total": 50.98,
     *       "product_snapshot": {
     *         "name": "Premium Cotton T-Shirt",
     *         "description": "High-quality cotton...",
     *         "images": ["products/tshirt-black-front.jpg"],
     *         "captured_at": ISODate("2025-09-23T...")
     *       }
     *     }
     *   ],
     *   "pricing": {
     *     "subtotal": 50.98,
     *     "tax": {
     *       "rate": 0.08,
     *       "amount": 4.08,
     *       "breakdown": [
     *         {
     *           "type": "sales_tax",
     *           "rate": 0.08,
     *           "amount": 4.08,
     *           "jurisdiction": "New York State"
     *         }
     *       ]
     *     },
     *     "shipping_cost": 5.99,
     *     "discounts": [
     *       {
     *         "type": "coupon",
     *         "code": "SAVE15",
     *         "description": "15% off first order",
     *         "amount": 7.65
     *       }
     *     ],
     *     "total_discount": 7.65,
     *     "grand_total": 53.40,
     *     "currency": "USD"
     *   },
     *   "shipping": {
     *     "address": {
     *       "recipient_name": "John Doe",
     *       "street": "123 Main St",
     *       "city": "New York",
     *       "state": "NY",
     *       "postal_code": "10001",
     *       "country": "USA",
     *       "coordinates": {
     *         "type": "Point",
     *         "coordinates": [-73.935242, 40.730610]
     *       }
     *     },
     *     "method": {
     *       "carrier": "FedEx",
     *       "service": "Ground",
     *       "estimated_days": "3-5 business days",
     *       "cost": 5.99
     *     },
     *     "tracking": {
     *       "number": "1234567890123456",
     *       "url": "https://fedex.com/track/1234567890123456",
     *       "status": "in_transit",
     *       "estimated_delivery": ISODate("2025-09-26T17:00:00Z"),
     *       "updates": [
     *         {
     *           "status": "shipped",
     *           "location": "New York, NY",
     *           "timestamp": ISODate("2025-09-23T14:30:00Z"),
     *           "description": "Package shipped from warehouse"
     *         }
     *       ]
     *     }
     *   },
     *   "payment": {
     *     "method": "credit_card",
     *     "provider": "stripe",
     *     "transaction_id": "pi_1234567890",
     *     "card_info": {
     *       "last_four": "4242",
     *       "brand": "visa",
     *       "exp_month": 12,
     *       "exp_year": 2026
     *     },
     *     "amount_charged": 53.40,
     *     "currency": "USD",
     *     "status": "paid",
     *     "paid_at": ISODate("2025-09-23T12:15:30Z"),
     *     "authorization_code": "AUTH123456",
     *     "risk_score": 15,
     *     "processor_response": {
     *       "code": "approved",
     *       "message": "Transaction approved"
     *     }
     *   },
     *   "fulfillment": {
     *     "warehouse_id": "WH_NYC",
     *     "assigned_to": ObjectId("..."), // Staff member
     *     "pick_list_generated": ISODate("2025-09-23T13:00:00Z"),
     *     "picked_at": ISODate("2025-09-23T13:45:00Z"),
     *     "packed_at": ISODate("2025-09-23T14:00:00Z"),
     *     "shipped_at": ISODate("2025-09-23T14:30:00Z"),
     *     "delivery_attempts": [
     *       {
     *         "attempt": 1,
     *         "timestamp": ISODate("2025-09-26T16:30:00Z"),
     *         "status": "delivered",
     *         "signature": "J. Doe",
     *         "notes": "Delivered to front door"
     *       }
     *     ]
     *   },
     *   "metadata": {
     *     "source": "web",
     *     "device": "desktop",
     *     "browser": "Chrome 118",
     *     "utm_source": "google",
     *     "utm_campaign": "fall_sale",
     *     "session_id": "sess_1234567890",
     *     "ip_address": "192.168.1.1",
     *     "customer_notes": "Please deliver between 2-5 PM",
     *     "internal_notes": "VIP customer - priority handling",
     *     "tags": ["first_time_customer", "high_value"],
     *     "estimated_profit": 15.25,
     *     "customer_acquisition_cost": 8.50
     *   },
     *   "audit_log": [
     *     {
     *       "action": "created",
     *       "user_id": ObjectId("..."),
     *       "timestamp": ISODate("2025-09-23T12:15:30Z"),
     *       "details": "Order created by customer",
     *       "ip_address": "192.168.1.1"
     *     },
     *     {
     *       "action": "payment_processed",
     *       "user_id": ObjectId("..."),
     *       "timestamp": ISODate("2025-09-23T12:15:35Z"),
     *       "details": "Payment of $53.40 processed successfully"
     *     },
     *     {
     *       "action": "status_changed",
     *       "user_id": ObjectId("..."), // Staff member
     *       "timestamp": ISODate("2025-09-23T13:00:00Z"),
     *       "details": "Status changed from 'pending' to 'processing'",
     *       "old_status": "pending",
     *       "new_status": "processing"
     *     }
     *   ],
     *   "created_at": ISODate("2025-09-23T12:15:30Z"),
     *   "updated_at": ISODate("2025-09-23T14:30:00Z")
     * }
     */

    // Order status constants
    const STATUS_PENDING = 'pending';
    const STATUS_PAYMENT_PROCESSING = 'payment_processing';
    const STATUS_PAID = 'paid';
    const STATUS_PROCESSING = 'processing';
    const STATUS_SHIPPED = 'shipped';
    const STATUS_DELIVERED = 'delivered';
    const STATUS_CANCELLED = 'cancelled';
    const STATUS_REFUNDED = 'refunded';

    /**
     * Advanced MongoDB Indexes for Orders:
     * 
     * 1. Compound Index for user orders with status
     * db.orders.createIndex({ "user_id": 1, "status": 1, "created_at": -1 })
     * 
     * 2. Index for order number lookup
     * db.orders.createIndex({ "order_number": 1 }, { unique: true })
     * 
     * 3. Geospatial Index for shipping locations
     * db.orders.createIndex({ "shipping.address.coordinates": "2dsphere" })
     * 
     * 4. Index for payment tracking
     * db.orders.createIndex({ "payment.transaction_id": 1, "payment.provider": 1 })
     * 
     * 5. Compound Index for business analytics
     * db.orders.createIndex({ 
     *   "status": 1, 
     *   "created_at": -1, 
     *   "pricing.grand_total": -1 
     * })
     * 
     * 6. Index for fulfillment operations
     * db.orders.createIndex({ 
     *   "fulfillment.warehouse_id": 1, 
     *   "fulfillment.assigned_to": 1,
     *   "status": 1 
     * })
     * 
     * 7. TTL Index for abandoned cart cleanup
     * db.orders.createIndex({ "updated_at": 1 }, { 
     *   expireAfterSeconds: 2592000, // 30 days
     *   partialFilterExpression: { "status": "pending" }
     * })
     */

    /**
     * Advanced Aggregation for Sales Analytics
     */
    public static function getSalesAnalytics($startDate = null, $endDate = null, $groupBy = 'day')
    {
        $startDate = $startDate ?: now()->subMonths(3);
        $endDate = $endDate ?: now();

        return self::raw(function ($collection) use ($startDate, $endDate, $groupBy) {
            $dateFormat = match($groupBy) {
                'hour' => '%Y-%m-%d-%H',
                'day' => '%Y-%m-%d',
                'week' => '%Y-%U',
                'month' => '%Y-%m',
                'year' => '%Y',
                default => '%Y-%m-%d'
            };

            return $collection->aggregate([
                // Match orders in date range
                [
                    '$match' => [
                        'status' => ['$in' => ['paid', 'processing', 'shipped', 'delivered']],
                        'created_at' => [
                            '$gte' => new \MongoDB\BSON\UTCDateTime($startDate->getTimestamp() * 1000),
                            '$lte' => new \MongoDB\BSON\UTCDateTime($endDate->getTimestamp() * 1000)
                        ]
                    ]
                ],
                
                // Add calculated fields
                [
                    '$addFields' => [
                        'order_date' => [
                            '$dateToString' => [
                                'format' => $dateFormat,
                                'date' => '$created_at'
                            ]
                        ],
                        'profit' => [
                            '$subtract' => [
                                '$pricing.grand_total',
                                ['$add' => ['$pricing.shipping_cost', '$metadata.customer_acquisition_cost']]
                            ]
                        ],
                        'items_count' => ['$size' => '$items']
                    ]
                ],
                
                // Group by date and calculate metrics
                [
                    '$group' => [
                        '_id' => '$order_date',
                        'total_orders' => ['$sum' => 1],
                        'total_revenue' => ['$sum' => '$pricing.grand_total'],
                        'total_profit' => ['$sum' => '$profit'],
                        'avg_order_value' => ['$avg' => '$pricing.grand_total'],
                        'total_items_sold' => ['$sum' => '$items_count'],
                        'unique_customers' => ['$addToSet' => '$user_id'],
                        'payment_methods' => ['$push' => '$payment.method'],
                        'shipping_methods' => ['$push' => '$shipping.method.carrier'],
                        'order_sources' => ['$push' => '$metadata.source']
                    ]
                ],
                
                // Add calculated fields for final metrics
                [
                    '$addFields' => [
                        'unique_customers_count' => ['$size' => '$unique_customers'],
                        'profit_margin' => [
                            '$cond' => [
                                'if' => ['$gt' => ['$total_revenue', 0]],
                                'then' => [
                                    '$multiply' => [
                                        ['$divide' => ['$total_profit', '$total_revenue']],
                                        100
                                    ]
                                ],
                                'else' => 0
                            ]
                        ],
                        'payment_method_breakdown' => [
                            '$arrayToObject' => [
                                '$map' => [
                                    'input' => [
                                        '$setUnion' => ['$payment_methods']
                                    ],
                                    'as' => 'method',
                                    'in' => [
                                        'k' => '$$method',
                                        'v' => [
                                            '$size' => [
                                                '$filter' => [
                                                    'input' => '$payment_methods',
                                                    'cond' => ['$eq' => ['$$this', '$$method']]
                                                ]
                                            ]
                                        ]
                                    ]
                                ]
                            ]
                        ]
                    ]
                ],
                
                // Sort by date
                [
                    '$sort' => ['_id' => 1]
                ],
                
                // Format output
                [
                    '$project' => [
                        'date' => '$_id',
                        'metrics' => [
                            'total_orders' => '$total_orders',
                            'total_revenue' => ['$round' => ['$total_revenue', 2]],
                            'total_profit' => ['$round' => ['$total_profit', 2]],
                            'avg_order_value' => ['$round' => ['$avg_order_value', 2]],
                            'total_items_sold' => '$total_items_sold',
                            'unique_customers' => '$unique_customers_count',
                            'profit_margin' => ['$round' => ['$profit_margin', 2]]
                        ],
                        'breakdown' => [
                            'payment_methods' => '$payment_method_breakdown',
                            'shipping_carriers' => [
                                '$arrayToObject' => [
                                    '$map' => [
                                        'input' => [
                                            '$setUnion' => ['$shipping_methods']
                                        ],
                                        'as' => 'carrier',
                                        'in' => [
                                            'k' => '$$carrier',
                                            'v' => [
                                                '$size' => [
                                                    '$filter' => [
                                                        'input' => '$shipping_methods',
                                                        'cond' => ['$eq' => ['$$this', '$$carrier']]
                                                    ]
                                                ]
                                            ]
                                        ]
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            ]);
        });
    }

    /**
     * Complex Aggregation for Customer Lifetime Value Analysis
     */
    public static function getCustomerLifetimeValue($userId = null)
    {
        return self::raw(function ($collection) use ($userId) {
            $matchStage = [
                'status' => ['$in' => ['paid', 'processing', 'shipped', 'delivered']]
            ];

            if ($userId) {
                $matchStage['user_id'] = new \MongoDB\BSON\ObjectId($userId);
            }

            return $collection->aggregate([
                ['$match' => $matchStage],
                
                // Group by customer
                [
                    '$group' => [
                        '_id' => '$user_id',
                        'total_orders' => ['$sum' => 1],
                        'total_spent' => ['$sum' => '$pricing.grand_total'],
                        'avg_order_value' => ['$avg' => '$pricing.grand_total'],
                        'first_order_date' => ['$min' => '$created_at'],
                        'last_order_date' => ['$max' => '$created_at'],
                        'total_items_purchased' => [
                            '$sum' => ['$size' => '$items']
                        ],
                        'preferred_categories' => [
                            '$push' => [
                                '$map' => [
                                    'input' => '$items',
                                    'in' => '$$this.category'
                                ]
                            ]
                        ],
                        'payment_methods_used' => [
                            '$addToSet' => '$payment.method'
                        ],
                        'shipping_addresses' => [
                            '$addToSet' => [
                                'city' => '$shipping.address.city',
                                'state' => '$shipping.address.state'
                            ]
                        ]
                    ]
                ],
                
                // Calculate customer lifetime metrics
                [
                    '$addFields' => [
                        'customer_lifetime_days' => [
                            '$divide' => [
                                ['$subtract' => ['$last_order_date', '$first_order_date']],
                                86400000  // Convert to days
                            ]
                        ],
                        'preferred_categories_flattened' => [
                            '$reduce' => [
                                'input' => '$preferred_categories',
                                'initialValue' => [],
                                'in' => ['$concatArrays' => ['$$value', '$$this']]
                            ]
                        ]
                    ]
                ],
                
                // Calculate advanced metrics
                [
                    '$addFields' => [
                        'order_frequency' => [
                            '$cond' => [
                                'if' => ['$gt' => ['$customer_lifetime_days', 0]],
                                'then' => [
                                    '$divide' => ['$total_orders', '$customer_lifetime_days']
                                ],
                                'else' => 0
                            ]
                        ],
                        'predicted_ltv' => [
                            '$multiply' => [
                                '$avg_order_value',
                                [
                                    '$multiply' => [
                                        ['$divide' => ['$total_orders', '$customer_lifetime_days']],
                                        365  // Project to yearly frequency
                                    ]
                                ],
                                3  // Assume 3-year customer lifetime
                            ]
                        ],
                        'top_category' => [
                            '$arrayElemAt' => [
                                [
                                    '$map' => [
                                        'input' => [
                                            '$slice' => [
                                                [
                                                    '$sortArray' => [
                                                        'input' => [
                                                            '$objectToArray' => [
                                                                '$arrayToObject' => [
                                                                    '$map' => [
                                                                        'input' => [
                                                                            '$setUnion' => ['$preferred_categories_flattened']
                                                                        ],
                                                                        'as' => 'category',
                                                                        'in' => [
                                                                            'k' => '$$category',
                                                                            'v' => [
                                                                                '$size' => [
                                                                                    '$filter' => [
                                                                                        'input' => '$preferred_categories_flattened',
                                                                                        'cond' => ['$eq' => ['$$this', '$$category']]
                                                                                    ]
                                                                                ]
                                                                            ]
                                                                        ]
                                                                    ]
                                                                ]
                                                            ]
                                                        ],
                                                        'sortBy' => ['v' => -1]
                                                    ]
                                                ],
                                                1
                                            ]
                                        ],
                                        'in' => '$$this.k'
                                    ]
                                ],
                                0
                            ]
                        ]
                    ]
                ],
                
                // Classify customers into segments
                [
                    '$addFields' => [
                        'customer_segment' => [
                            '$switch' => [
                                'branches' => [
                                    [
                                        'case' => [
                                            '$and' => [
                                                ['$gte' => ['$total_spent', 2000]],
                                                ['$gte' => ['$total_orders', 10]],
                                                ['$gte' => ['$order_frequency', 0.1]]
                                            ]
                                        ],
                                        'then' => 'Champions'
                                    ],
                                    [
                                        'case' => [
                                            '$and' => [
                                                ['$gte' => ['$total_spent', 1000]],
                                                ['$gte' => ['$total_orders', 5]]
                                            ]
                                        ],
                                        'then' => 'Loyal Customers'
                                    ],
                                    [
                                        'case' => [
                                            '$gte' => ['$avg_order_value', 150]
                                        ],
                                        'then' => 'Potential Loyalists'
                                    ],
                                    [
                                        'case' => [
                                            '$lte' => ['$order_frequency', 0.01]
                                        ],
                                        'then' => 'At Risk'
                                    ]
                                ],
                                'default' => 'Developing'
                            ]
                        ]
                    ]
                ],
                
                // Sort by total spent
                [
                    '$sort' => ['total_spent' => -1]
                ],
                
                // Format final output
                [
                    '$project' => [
                        'customer_id' => '$_id',
                        'segment' => '$customer_segment',
                        'metrics' => [
                            'total_orders' => '$total_orders',
                            'total_spent' => ['$round' => ['$total_spent', 2]],
                            'avg_order_value' => ['$round' => ['$avg_order_value', 2]],
                            'customer_lifetime_days' => ['$round' => ['$customer_lifetime_days', 0]],
                            'order_frequency_per_day' => ['$round' => ['$order_frequency', 4]],
                            'predicted_ltv' => ['$round' => ['$predicted_ltv', 2]],
                            'total_items_purchased' => '$total_items_purchased'
                        ],
                        'preferences' => [
                            'top_category' => '$top_category',
                            'payment_methods' => '$payment_methods_used',
                            'shipping_locations' => '$shipping_addresses'
                        ],
                        'timeline' => [
                            'first_order' => '$first_order_date',
                            'last_order' => '$last_order_date'
                        ]
                    ]
                ]
            ]);
        });
    }

    /**
     * Real-time Order Tracking Pipeline
     */
    public static function getOrderTracking($orderNumber)
    {
        return self::raw(function ($collection) use ($orderNumber) {
            return $collection->aggregate([
                [
                    '$match' => [
                        'order_number' => $orderNumber
                    ]
                ],
                
                // Add calculated tracking status
                [
                    '$addFields' => [
                        'current_stage' => [
                            '$switch' => [
                                'branches' => [
                                    [
                                        'case' => ['$eq' => ['$status', 'pending']],
                                        'then' => [
                                            'stage' => 'Order Placed',
                                            'description' => 'Order has been placed and is awaiting payment',
                                            'progress_percentage' => 10
                                        ]
                                    ],
                                    [
                                        'case' => ['$eq' => ['$status', 'paid']],
                                        'then' => [
                                            'stage' => 'Payment Confirmed',
                                            'description' => 'Payment has been processed successfully',
                                            'progress_percentage' => 25
                                        ]
                                    ],
                                    [
                                        'case' => ['$eq' => ['$status', 'processing']],
                                        'then' => [
                                            'stage' => 'Order Processing',
                                            'description' => 'Your order is being prepared for shipment',
                                            'progress_percentage' => 50
                                        ]
                                    ],
                                    [
                                        'case' => ['$eq' => ['$status', 'shipped']],
                                        'then' => [
                                            'stage' => 'Shipped',
                                            'description' => 'Your order has been shipped',
                                            'progress_percentage' => 75
                                        ]
                                    ],
                                    [
                                        'case' => ['$eq' => ['$status', 'delivered']],
                                        'then' => [
                                            'stage' => 'Delivered',
                                            'description' => 'Your order has been delivered',
                                            'progress_percentage' => 100
                                        ]
                                    ]
                                ],
                                'default' => [
                                    'stage' => 'Unknown',
                                    'description' => 'Order status unknown',
                                    'progress_percentage' => 0
                                ]
                            ]
                        ],
                        'estimated_delivery_date' => [
                            '$cond' => [
                                'if' => ['$ne' => ['$shipping.tracking.estimated_delivery', null]],
                                'then' => '$shipping.tracking.estimated_delivery',
                                'else' => [
                                    '$dateAdd' => [
                                        'startDate' => '$created_at',
                                        'unit' => 'day',
                                        'amount' => 7  // Default 7 days if no estimate
                                    ]
                                ]
                            ]
                        ]
                    ]
                ],
                
                // Project relevant tracking information
                [
                    '$project' => [
                        'order_number' => 1,
                        'status' => 1,
                        'current_stage' => 1,
                        'estimated_delivery' => '$estimated_delivery_date',
                        'tracking_info' => '$shipping.tracking',
                        'items_summary' => [
                            '$map' => [
                                'input' => '$items',
                                'in' => [
                                    'name' => '$$this.name',
                                    'quantity' => '$$this.quantity',
                                    'size' => '$$this.size',
                                    'color' => '$$this.color.name'
                                ]
                            ]
                        ],
                        'shipping_address' => '$shipping.address',
                        'timeline' => '$audit_log',
                        'created_at' => 1
                    ]
                ]
            ]);
        });
    }

    /**
     * Inventory Impact Analysis
     */
    public static function getInventoryImpact($startDate = null, $endDate = null)
    {
        $startDate = $startDate ?: now()->subMonths(1);
        $endDate = $endDate ?: now();

        return self::raw(function ($collection) use ($startDate, $endDate) {
            return $collection->aggregate([
                [
                    '$match' => [
                        'status' => ['$in' => ['paid', 'processing', 'shipped', 'delivered']],
                        'created_at' => [
                            '$gte' => new \MongoDB\BSON\UTCDateTime($startDate->getTimestamp() * 1000),
                            '$lte' => new \MongoDB\BSON\UTCDateTime($endDate->getTimestamp() * 1000)
                        ]
                    ]
                ],
                
                // Unwind items to analyze each product
                [
                    '$unwind' => '$items'
                ],
                
                // Group by product and variant
                [
                    '$group' => [
                        '_id' => [
                            'product_id' => '$items.product_id',
                            'sku' => '$items.sku'
                        ],
                        'total_quantity_sold' => ['$sum' => '$items.quantity'],
                        'total_revenue' => ['$sum' => '$items.total'],
                        'avg_sale_price' => ['$avg' => '$items.final_price'],
                        'order_count' => ['$sum' => 1],
                        'product_name' => ['$first' => '$items.name'],
                        'size' => ['$first' => '$items.size'],
                        'color' => ['$first' => '$items.color.name']
                    ]
                ],
                
                // Sort by quantity sold
                [
                    '$sort' => ['total_quantity_sold' => -1]
                ],
                
                // Group back to get overall statistics
                [
                    '$group' => [
                        '_id' => null,
                        'top_selling_products' => [
                            '$push' => [
                                'product_id' => '$_id.product_id',
                                'sku' => '$_id.sku',
                                'name' => '$product_name',
                                'size' => '$size',
                                'color' => '$color',
                                'quantity_sold' => '$total_quantity_sold',
                                'revenue' => '$total_revenue',
                                'avg_price' => '$avg_sale_price',
                                'order_count' => '$order_count'
                            ]
                        ],
                        'total_items_sold' => ['$sum' => '$total_quantity_sold'],
                        'total_revenue' => ['$sum' => '$total_revenue']
                    ]
                ],
                
                // Limit top products and format
                [
                    '$project' => [
                        'summary' => [
                            'total_items_sold' => '$total_items_sold',
                            'total_revenue' => ['$round' => ['$total_revenue', 2]]
                        ],
                        'top_selling_products' => ['$slice' => ['$top_selling_products', 20]]
                    ]
                ]
            ]);
        });
    }

    // Query Scopes
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', new \MongoDB\BSON\ObjectId($userId));
    }

    public function scopePaid($query)
    {
        return $query->whereIn('status', ['paid', 'processing', 'shipped', 'delivered']);
    }

    public function scopeInDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('created_at', [
            new \MongoDB\BSON\UTCDateTime($startDate->getTimestamp() * 1000),
            new \MongoDB\BSON\UTCDateTime($endDate->getTimestamp() * 1000)
        ]);
    }

    // Helper Methods
    public function updateStatus($newStatus, $userId = null, $notes = null)
    {
        $oldStatus = $this->status;
        $this->status = $newStatus;
        
        // Add audit log entry
        $auditEntry = [
            'action' => 'status_changed',
            'user_id' => $userId ? new \MongoDB\BSON\ObjectId($userId) : null,
            'timestamp' => new \MongoDB\BSON\UTCDateTime(),
            'details' => $notes ?: "Status changed from '{$oldStatus}' to '{$newStatus}'",
            'old_status' => $oldStatus,
            'new_status' => $newStatus
        ];

        $auditLog = $this->audit_log ?: [];
        $auditLog[] = $auditEntry;
        $this->audit_log = $auditLog;
        
        return $this->save();
    }

    public function addTrackingUpdate($status, $location, $description)
    {
        $shipping = $this->shipping ?: [];
        $tracking = $shipping['tracking'] ?: [];
        $updates = $tracking['updates'] ?: [];

        $updates[] = [
            'status' => $status,
            'location' => $location,
            'timestamp' => new \MongoDB\BSON\UTCDateTime(),
            'description' => $description
        ];

        $tracking['updates'] = $updates;
        $tracking['status'] = $status;
        $shipping['tracking'] = $tracking;
        $this->shipping = $shipping;

        return $this->save();
    }

    public function getTotalItems()
    {
        return collect($this->items)->sum('quantity');
    }

    public function getGrandTotal()
    {
        return $this->pricing['grand_total'] ?? 0;
    }

    public function canBeCancelled()
    {
        return in_array($this->status, ['pending', 'paid', 'processing']);
    }

    public function canBeRefunded()
    {
        return in_array($this->status, ['paid', 'processing', 'shipped', 'delivered']);
    }

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public static function generateOrderNumber()
    {
        return 'ORD-' . date('Y') . '-' . str_pad(random_int(1, 999999), 6, '0', STR_PAD_LEFT);
    }
}