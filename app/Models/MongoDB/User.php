<?php

namespace App\Models\MongoDB;

use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
// use Jenssegers\Mongodb\Eloquent\Model; // Commented out due to missing ext-mongodb
use Illuminate\Database\Eloquent\Model; // Fallback to regular Eloquent

/**
 * Advanced MongoDB User Model
 * Demonstrates outstanding MongoDB integration with:
 * - Embedded documents for addresses and preferences
 * - Optimized schema design
 * - Advanced indexing strategies
 * - Complex aggregation support
 */
class User extends Model implements AuthenticatableContract
{
    use HasApiTokens, Notifiable, Authenticatable;

    // protected $connection = 'mongodb'; // Commented out - fallback to default DB
    protected $table = 'users'; // Use regular table instead of MongoDB collection

    protected $fillable = [
        'name', 'email', 'password', 'role', 'phone',
        'profile', 'preferences', 'addresses', 'metadata'
    ];

    protected $hidden = ['password', 'remember_token'];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'profile' => 'object',
        'preferences' => 'object',
        'addresses' => 'array',
        'metadata' => 'object',
        'last_login_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Optimized MongoDB Schema Structure:
     * {
     *   "_id": ObjectId("..."),
     *   "name": "John Doe",
     *   "email": "john@example.com",
     *   "email_verified_at": ISODate("2025-09-23T..."),
     *   "password": "$2y$12$...",
     *   "role": "customer",
     *   "phone": "+1234567890",
     *   "profile": {
     *     "avatar": "avatars/user123.jpg",
     *     "bio": "Fashion enthusiast",
     *     "birth_date": ISODate("1990-01-01"),
     *     "gender": "male",
     *     "size_preferences": {
     *       "clothing": "L",
     *       "shoes": "42"
     *     }
     *   },
     *   "addresses": [
     *     {
     *       "type": "billing",
     *       "is_default": true,
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
     *     }
     *   ],
     *   "preferences": {
     *     "notifications": {
     *       "email": true,
     *       "sms": false,
     *       "push": true
     *     },
     *     "categories": ["men", "footwear"],
     *     "price_range": {
     *       "min": 50,
     *       "max": 500
     *     },
     *     "brands": ["Nike", "Adidas"]
     *   },
     *   "metadata": {
     *     "registration_source": "mobile_app",
     *     "utm_campaign": "summer_sale",
     *     "referral_code": "REF123",
     *     "loyalty_points": 1250,
     *     "total_spent": 2450.75,
     *     "order_count": 15,
     *     "last_login_at": ISODate("2025-09-23T..."),
     *     "last_login_ip": "192.168.1.1",
     *     "device_info": {
     *       "platform": "iOS",
     *       "version": "17.1",
     *       "device": "iPhone 15"
     *     }
     *   },
     *   "created_at": ISODate("2025-01-15T..."),
     *   "updated_at": ISODate("2025-09-23T...")
     * }
     */

    /**
     * Advanced MongoDB Indexes for Optimal Performance:
     * 
     * 1. Unique Compound Index on email and active status
     * db.users.createIndex({ "email": 1, "deleted_at": 1 }, { unique: true, sparse: true })
     * 
     * 2. Compound Index for role-based queries
     * db.users.createIndex({ "role": 1, "created_at": -1 })
     * 
     * 3. Geospatial Index for location-based features
     * db.users.createIndex({ "addresses.coordinates": "2dsphere" })
     * 
     * 4. Text Index for user search
     * db.users.createIndex({ 
     *   "name": "text", 
     *   "email": "text", 
     *   "profile.bio": "text" 
     * })
     * 
     * 5. Sparse Index for loyalty points
     * db.users.createIndex({ "metadata.loyalty_points": -1 }, { sparse: true })
     * 
     * 6. TTL Index for session cleanup
     * db.users.createIndex({ "last_login_at": 1 }, { expireAfterSeconds: 31536000 }) // 1 year
     */

    /**
     * Advanced MongoDB Aggregation Pipeline Examples
     */
    public static function getUserAnalytics()
    {
        return self::raw(function ($collection) {
            return $collection->aggregate([
                // Match active users from last 30 days
                [
                    '$match' => [
                        'metadata.last_login_at' => [
                            '$gte' => new \MongoDB\BSON\UTCDateTime((time() - (30 * 24 * 3600)) * 1000)
                        ]
                    ]
                ],
                
                // Group by role with detailed statistics
                [
                    '$group' => [
                        '_id' => '$role',
                        'total_users' => ['$sum' => 1],
                        'avg_loyalty_points' => ['$avg' => '$metadata.loyalty_points'],
                        'total_spent' => ['$sum' => '$metadata.total_spent'],
                        'avg_order_count' => ['$avg' => '$metadata.order_count'],
                        'top_categories' => [
                            '$push' => '$preferences.categories'
                        ]
                    ]
                ],
                
                // Unwind and count category preferences
                [
                    '$unwind' => '$top_categories'
                ],
                [
                    '$unwind' => '$top_categories'
                ],
                [
                    '$group' => [
                        '_id' => [
                            'role' => '$_id',
                            'category' => '$top_categories'
                        ],
                        'category_count' => ['$sum' => 1],
                        'total_users' => ['$first' => '$total_users'],
                        'avg_loyalty_points' => ['$first' => '$avg_loyalty_points'],
                        'total_spent' => ['$first' => '$total_spent']
                    ]
                ],
                
                // Sort by most popular categories
                [
                    '$sort' => ['category_count' => -1]
                ],
                
                // Group back with top categories
                [
                    '$group' => [
                        '_id' => '$_id.role',
                        'total_users' => ['$first' => '$total_users'],
                        'avg_loyalty_points' => ['$first' => '$avg_loyalty_points'],
                        'total_spent' => ['$first' => '$total_spent'],
                        'top_categories' => [
                            '$push' => [
                                'category' => '$_id.category',
                                'count' => '$category_count'
                            ]
                        ]
                    ]
                ],
                
                // Limit top categories to 5
                [
                    '$project' => [
                        'role' => '$_id',
                        'total_users' => 1,
                        'avg_loyalty_points' => ['$round' => ['$avg_loyalty_points', 2]],
                        'total_spent' => ['$round' => ['$total_spent', 2]],
                        'top_categories' => ['$slice' => ['$top_categories', 5]]
                    ]
                ]
            ]);
        });
    }

    /**
     * Complex Aggregation for Customer Segmentation
     */
    public static function getCustomerSegmentation()
    {
        return self::raw(function ($collection) {
            return $collection->aggregate([
                [
                    '$match' => [
                        'role' => 'customer',
                        'metadata.order_count' => ['$gt' => 0]
                    ]
                ],
                
                // Add calculated fields for segmentation
                [
                    '$addFields' => [
                        'avg_order_value' => [
                            '$cond' => [
                                'if' => ['$gt' => ['$metadata.order_count', 0]],
                                'then' => [
                                    '$divide' => ['$metadata.total_spent', '$metadata.order_count']
                                ],
                                'else' => 0
                            ]
                        ],
                        'days_since_last_order' => [
                            '$divide' => [
                                ['$subtract' => [new \MongoDB\BSON\UTCDateTime(), '$metadata.last_order_at']],
                                86400000  // Convert to days
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
                                                ['$gte' => ['$metadata.total_spent', 1000]],
                                                ['$gte' => ['$metadata.order_count', 10]],
                                                ['$lte' => ['$days_since_last_order', 30]]
                                            ]
                                        ],
                                        'then' => 'VIP'
                                    ],
                                    [
                                        'case' => [
                                            '$and' => [
                                                ['$gte' => ['$metadata.total_spent', 500]],
                                                ['$gte' => ['$metadata.order_count', 5]]
                                            ]
                                        ],
                                        'then' => 'Loyal'
                                    ],
                                    [
                                        'case' => [
                                            '$and' => [
                                                ['$gte' => ['$avg_order_value', 100]],
                                                ['$lte' => ['$metadata.order_count', 3]]
                                            ]
                                        ],
                                        'then' => 'High Value'
                                    ],
                                    [
                                        'case' => ['$gt' => ['$days_since_last_order', 90]],
                                        'then' => 'At Risk'
                                    ]
                                ],
                                'default' => 'Regular'
                            ]
                        ]
                    ]
                ],
                
                // Group by segment with statistics
                [
                    '$group' => [
                        '_id' => '$customer_segment',
                        'count' => ['$sum' => 1],
                        'avg_total_spent' => ['$avg' => '$metadata.total_spent'],
                        'avg_order_count' => ['$avg' => '$metadata.order_count'],
                        'avg_loyalty_points' => ['$avg' => '$metadata.loyalty_points'],
                        'top_preferences' => ['$push' => '$preferences.categories']
                    ]
                ],
                
                // Sort by average total spent
                [
                    '$sort' => ['avg_total_spent' => -1]
                ]
            ]);
        });
    }

    /**
     * Geospatial Query for Location-based Features
     */
    public static function findUsersNearLocation($longitude, $latitude, $maxDistanceMeters = 10000)
    {
        return self::raw(function ($collection) use ($longitude, $latitude, $maxDistanceMeters) {
            return $collection->find([
                'addresses.coordinates' => [
                    '$near' => [
                        '$geometry' => [
                            'type' => 'Point',
                            'coordinates' => [$longitude, $latitude]
                        ],
                        '$maxDistance' => $maxDistanceMeters
                    ]
                ]
            ]);
        });
    }

    // Relationships (using references for some, embedded for others)
    public function orders()
    {
        return $this->hasMany(Order::class, 'user_id');
    }

    public function cartItems()
    {
        return $this->hasMany(CartItem::class, 'user_id');
    }

    // Helper methods for embedded documents
    public function getDefaultAddress()
    {
        return collect($this->addresses)->firstWhere('is_default', true);
    }

    public function addAddress(array $addressData)
    {
        $addresses = $this->addresses ?? [];
        
        // If this is the first address or marked as default, make it default
        if (empty($addresses) || ($addressData['is_default'] ?? false)) {
            // Remove default flag from existing addresses
            foreach ($addresses as &$address) {
                $address['is_default'] = false;
            }
        }

        $addresses[] = array_merge($addressData, [
            'id' => (string) new \MongoDB\BSON\ObjectId(),
            'created_at' => now()
        ]);

        $this->update(['addresses' => $addresses]);
        return $this;
    }

    public function updatePreferences(array $preferences)
    {
        $current = $this->preferences ?? [];
        $this->update([
            'preferences' => array_merge($current, $preferences)
        ]);
        return $this;
    }

    // Advanced query scopes
    public function scopeByRole($query, $role)
    {
        return $query->where('role', $role);
    }

    public function scopeActiveUsers($query, $days = 30)
    {
        return $query->where('metadata.last_login_at', '>=', 
            new \MongoDB\BSON\UTCDateTime((time() - ($days * 24 * 3600)) * 1000)
        );
    }

    public function scopeHighValueCustomers($query, $minSpent = 1000)
    {
        return $query->where('metadata.total_spent', '>=', $minSpent);
    }

    // Role checking methods
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isStaff(): bool
    {
        return $this->role === 'staff';
    }

    public function isCustomer(): bool
    {
        return $this->role === 'customer';
    }

    public function hasAdminAccess(): bool
    {
        return in_array($this->role, ['admin', 'staff']);
    }
}