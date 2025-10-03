<?php

namespace App\Models\MongoDB;

use Jenssegers\Mongodb\Eloquent\Model;

/**
 * Advanced MongoDB Product Model
 * Demonstrates outstanding MongoDB integration with:
 * - Embedded documents for variants, reviews, and metadata
 * - Advanced text search capabilities
 * - Geospatial data for store locations
 * - Complex aggregation for analytics
 * - Optimized indexing strategies
 */
class Product extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'products';

    protected $fillable = [
        'name', 'slug', 'description', 'category', 'price', 'variants',
        'images', 'seo', 'inventory', 'reviews_summary', 'metadata'
    ];

    protected $casts = [
        'category' => 'object',
        'price' => 'object',
        'variants' => 'array',
        'images' => 'array', 
        'seo' => 'object',
        'inventory' => 'object',
        'reviews_summary' => 'object',
        'metadata' => 'object',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Optimized MongoDB Schema Structure:
     * {
     *   "_id": ObjectId("..."),
     *   "name": "Premium Cotton T-Shirt",
     *   "slug": "premium-cotton-t-shirt-mens",
     *   "description": "High-quality cotton t-shirt with modern fit",
     *   "category": {
     *     "id": "men_clothing",
     *     "name": "Men's Clothing",
     *     "hierarchy": ["men", "clothing", "shirts"],
     *     "attributes": {
     *       "gender": "men",
     *       "clothing_type": "casual",
     *       "season": ["spring", "summer"]
     *     }
     *   },
     *   "price": {
     *     "base": 29.99,
     *     "currency": "USD",
     *     "discount": {
     *       "type": "percentage",
     *       "value": 15,
     *       "valid_until": ISODate("2025-12-31T23:59:59Z")
     *     },
     *     "history": [
     *       {
     *         "price": 34.99,
     *         "from": ISODate("2025-01-01T00:00:00Z"),
     *         "to": ISODate("2025-06-30T23:59:59Z")
     *       }
     *     ]
     *   },
     *   "variants": [
     *     {
     *       "id": "variant_001",
     *       "sku": "TCM-001-S-BLK",
     *       "size": "S",
     *       "color": {
     *         "name": "Black",
     *         "hex": "#000000",
     *         "image": "colors/black-swatch.jpg"
     *       },
     *       "price_adjustment": 0.00,
     *       "inventory": {
     *         "quantity": 50,
     *         "reserved": 3,
     *         "available": 47,
     *         "warehouse_locations": [
     *           {
     *             "warehouse_id": "WH_NYC",
     *             "quantity": 30,
     *             "coordinates": {
     *               "type": "Point",
     *               "coordinates": [-73.935242, 40.730610]
     *             }
     *           }
     *         ]
     *       },
     *       "dimensions": {
     *         "weight": 0.15,
     *         "length": 70,
     *         "width": 50,
     *         "height": 1
     *       }
     *     }
     *   ],
     *   "images": [
     *     {
     *       "url": "products/tshirt-black-front.jpg",
     *       "alt": "Black t-shirt front view",
     *       "type": "primary",
     *       "variant_ids": ["variant_001"],
     *       "order": 1
     *     }
     *   ],
     *   "seo": {
     *     "meta_title": "Premium Cotton T-Shirt - Comfortable Men's Clothing",
     *     "meta_description": "Discover our premium cotton t-shirt...",
     *     "keywords": ["men's t-shirt", "cotton", "premium", "casual wear"],
     *     "structured_data": {
     *       "@type": "Product",
     *       "@context": "https://schema.org/",
     *       "name": "Premium Cotton T-Shirt"
     *     }
     *   },
     *   "reviews_summary": {
     *     "total_reviews": 127,
     *     "average_rating": 4.3,
     *     "rating_distribution": {
     *       "5": 68,
     *       "4": 32,
     *       "3": 15,
     *       "2": 8,
     *       "1": 4
     *     },
     *     "verified_purchases": 112,
     *     "recent_reviews": [
     *       {
     *         "user_id": ObjectId("..."),
     *         "rating": 5,
     *         "title": "Excellent quality!",
     *         "comment": "Very comfortable and good fit",
     *         "verified_purchase": true,
     *         "created_at": ISODate("2025-09-20T...")
     *       }
     *     ]
     *   },
     *   "metadata": {
     *     "brand": "Ares Apparel",
     *     "manufacturer": "Premium Textiles Ltd",
     *     "material": "100% Organic Cotton",
     *     "care_instructions": ["Machine wash cold", "Tumble dry low"],
     *     "tags": ["organic", "sustainable", "comfort"],
     *     "view_count": 2847,
     *     "purchase_count": 156,
     *     "conversion_rate": 0.055,
     *     "seasonal_performance": {
     *       "spring": { "views": 800, "sales": 45 },
     *       "summer": { "views": 1200, "sales": 78 }
     *     },
     *     "competitor_analysis": {
     *       "similar_products": [
     *         {
     *           "competitor": "Brand X",
     *           "price": 24.99,
     *           "rating": 4.1
     *         }
     *       ]
     *     },
     *     "created_by": ObjectId("..."),
     *     "last_updated_by": ObjectId("..."),
     *     "status": "active"
     *   },
     *   "created_at": ISODate("2025-01-15T..."),
     *   "updated_at": ISODate("2025-09-23T...")
     * }
     */

    /**
     * Advanced MongoDB Indexes for Optimal Performance:
     * 
     * 1. Compound Index for category and price filtering
     * db.products.createIndex({ 
     *   "category.hierarchy": 1, 
     *   "price.base": 1, 
     *   "metadata.status": 1 
     * })
     * 
     * 2. Text Index for product search
     * db.products.createIndex({
     *   "name": "text",
     *   "description": "text", 
     *   "category.name": "text",
     *   "metadata.tags": "text",
     *   "seo.keywords": "text"
     * }, {
     *   weights: {
     *     "name": 10,
     *     "description": 5,
     *     "category.name": 3,
     *     "metadata.tags": 2,
     *     "seo.keywords": 1
     *   }
     * })
     * 
     * 3. Geospatial Index for warehouse locations
     * db.products.createIndex({ "variants.inventory.warehouse_locations.coordinates": "2dsphere" })
     * 
     * 4. Sparse Index for discounted products
     * db.products.createIndex({ "price.discount.valid_until": 1 }, { sparse: true })
     * 
     * 5. Compound Index for popularity sorting
     * db.products.createIndex({ 
     *   "metadata.status": 1,
     *   "reviews_summary.average_rating": -1, 
     *   "metadata.purchase_count": -1 
     * })
     * 
     * 6. Index for inventory management
     * db.products.createIndex({ "variants.inventory.quantity": 1 })
     */

    /**
     * Advanced Aggregation Pipeline for Product Analytics
     */
    public static function getProductAnalytics($startDate = null, $endDate = null)
    {
        $startDate = $startDate ?: now()->subMonths(3);
        $endDate = $endDate ?: now();

        return self::raw(function ($collection) use ($startDate, $endDate) {
            return $collection->aggregate([
                // Match active products
                [
                    '$match' => [
                        'metadata.status' => 'active',
                        'created_at' => [
                            '$gte' => new \MongoDB\BSON\UTCDateTime($startDate->getTimestamp() * 1000),
                            '$lte' => new \MongoDB\BSON\UTCDateTime($endDate->getTimestamp() * 1000)
                        ]
                    ]
                ],
                
                // Add calculated fields
                [
                    '$addFields' => [
                        'total_inventory' => [
                            '$sum' => '$variants.inventory.quantity'
                        ],
                        'discounted_price' => [
                            '$cond' => [
                                'if' => [
                                    '$and' => [
                                        ['$ne' => ['$price.discount', null]],
                                        ['$gte' => ['$price.discount.valid_until', new \MongoDB\BSON\UTCDateTime()]]
                                    ]
                                ],
                                'then' => [
                                    '$subtract' => [
                                        '$price.base',
                                        [
                                            '$multiply' => [
                                                '$price.base',
                                                ['$divide' => ['$price.discount.value', 100]]
                                            ]
                                        ]
                                    ]
                                ],
                                'else' => '$price.base'
                            ]
                        ],
                        'performance_score' => [
                            '$add' => [
                                ['$multiply' => ['$reviews_summary.average_rating', 20]],
                                ['$multiply' => ['$metadata.conversion_rate', 1000]],
                                ['$divide' => ['$metadata.purchase_count', 10]]
                            ]
                        ]
                    ]
                ],
                
                // Group by category
                [
                    '$group' => [
                        '_id' => '$category.hierarchy.0',
                        'total_products' => ['$sum' => 1],
                        'avg_price' => ['$avg' => '$discounted_price'],
                        'total_inventory' => ['$sum' => '$total_inventory'],
                        'avg_rating' => ['$avg' => '$reviews_summary.average_rating'],
                        'total_views' => ['$sum' => '$metadata.view_count'],
                        'total_purchases' => ['$sum' => '$metadata.purchase_count'],
                        'avg_performance' => ['$avg' => '$performance_score'],
                        'top_performing_products' => [
                            '$push' => [
                                'name' => '$name',
                                'performance_score' => '$performance_score',
                                'purchase_count' => '$metadata.purchase_count'
                            ]
                        ]
                    ]
                ],
                
                // Calculate conversion rate
                [
                    '$addFields' => [
                        'category_conversion_rate' => [
                            '$cond' => [
                                'if' => ['$gt' => ['$total_views', 0]],
                                'then' => ['$divide' => ['$total_purchases', '$total_views']],
                                'else' => 0
                            ]
                        ]
                    ]
                ],
                
                // Sort top performing products
                [
                    '$addFields' => [
                        'top_performing_products' => [
                            '$slice' => [
                                [
                                    '$sortArray' => [
                                        'input' => '$top_performing_products',
                                        'sortBy' => ['performance_score' => -1]
                                    ]
                                ],
                                5
                            ]
                        ]
                    ]
                ],
                
                // Sort categories by performance
                [
                    '$sort' => ['avg_performance' => -1]
                ],
                
                // Format output
                [
                    '$project' => [
                        'category' => '$_id',
                        'metrics' => [
                            'total_products' => '$total_products',
                            'avg_price' => ['$round' => ['$avg_price', 2]],
                            'total_inventory' => '$total_inventory',
                            'avg_rating' => ['$round' => ['$avg_rating', 2]],
                            'total_views' => '$total_views',
                            'total_purchases' => '$total_purchases',
                            'conversion_rate' => ['$round' => [['$multiply' => ['$category_conversion_rate', 100]], 2]],
                            'performance_score' => ['$round' => ['$avg_performance', 2]]
                        ],
                        'top_performing_products' => 1
                    ]
                ]
            ]);
        });
    }

    /**
     * Complex Aggregation for Inventory Management
     */
    public static function getInventoryReport()
    {
        return self::raw(function ($collection) {
            return $collection->aggregate([
                [
                    '$match' => [
                        'metadata.status' => 'active'
                    ]
                ],
                
                // Unwind variants to analyze each SKU
                [
                    '$unwind' => '$variants'
                ],
                
                // Add calculated fields for inventory analysis
                [
                    '$addFields' => [
                        'stock_status' => [
                            '$switch' => [
                                'branches' => [
                                    [
                                        'case' => ['$lte' => ['$variants.inventory.available', 0]],
                                        'then' => 'out_of_stock'
                                    ],
                                    [
                                        'case' => ['$lte' => ['$variants.inventory.available', 10]],
                                        'then' => 'low_stock'
                                    ],
                                    [
                                        'case' => ['$lte' => ['$variants.inventory.available', 50]],
                                        'then' => 'medium_stock'
                                    ]
                                ],
                                'default' => 'high_stock'
                            ]
                        ],
                        'inventory_value' => [
                            '$multiply' => [
                                '$variants.inventory.quantity',
                                ['$add' => ['$price.base', '$variants.price_adjustment']]
                            ]
                        ]
                    ]
                ],
                
                // Group by stock status
                [
                    '$group' => [
                        '_id' => '$stock_status',
                        'count' => ['$sum' => 1],
                        'total_quantity' => ['$sum' => '$variants.inventory.quantity'],
                        'total_value' => ['$sum' => '$inventory_value'],
                        'products' => [
                            '$push' => [
                                'name' => '$name',
                                'sku' => '$variants.sku',
                                'size' => '$variants.size',
                                'color' => '$variants.color.name',
                                'available' => '$variants.inventory.available',
                                'reserved' => '$variants.inventory.reserved'
                            ]
                        ]
                    ]
                ],
                
                // Sort by priority (out of stock first)
                [
                    '$sort' => [
                        '_id' => 1
                    ]
                ],
                
                // Limit products list to 10 per status
                [
                    '$addFields' => [
                        'products' => ['$slice' => ['$products', 10]]
                    ]
                ]
            ]);
        });
    }

    /**
     * Geospatial Query for Warehouse-based Inventory
     */
    public static function findProductsNearLocation($longitude, $latitude, $maxDistanceMeters = 50000)
    {
        return self::raw(function ($collection) use ($longitude, $latitude, $maxDistanceMeters) {
            return $collection->aggregate([
                [
                    '$match' => [
                        'variants.inventory.warehouse_locations.coordinates' => [
                            '$near' => [
                                '$geometry' => [
                                    'type' => 'Point',
                                    'coordinates' => [$longitude, $latitude]
                                ],
                                '$maxDistance' => $maxDistanceMeters
                            ]
                        ]
                    ]
                ],
                [
                    '$addFields' => [
                        'nearby_warehouses' => [
                            '$filter' => [
                                'input' => '$variants',
                                'cond' => [
                                    '$anyElementTrue' => [
                                        '$map' => [
                                            'input' => '$$this.inventory.warehouse_locations',
                                            'in' => [
                                                '$lte' => [
                                                    [
                                                        '$geoNear' => [
                                                            'point' => [$longitude, $latitude],
                                                            'coordinates' => '$$this.coordinates.coordinates'
                                                        ]
                                                    ],
                                                    $maxDistanceMeters
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
     * Advanced Text Search with Faceted Results
     */
    public static function searchProducts($searchTerm, $filters = [])
    {
        return self::raw(function ($collection) use ($searchTerm, $filters) {
            $pipeline = [];

            // Text search stage
            if (!empty($searchTerm)) {
                $pipeline[] = [
                    '$match' => [
                        '$text' => [
                            '$search' => $searchTerm,
                            '$caseSensitive' => false,
                            '$diacriticSensitive' => false
                        ]
                    ]
                ];

                // Add text score for relevance
                $pipeline[] = [
                    '$addFields' => [
                        'relevance_score' => ['$meta' => 'textScore']
                    ]
                ];
            }

            // Apply filters
            $matchConditions = ['metadata.status' => 'active'];

            if (!empty($filters['category'])) {
                $matchConditions['category.hierarchy'] = ['$in' => $filters['category']];
            }

            if (!empty($filters['price_min']) || !empty($filters['price_max'])) {
                $priceRange = [];
                if (!empty($filters['price_min'])) {
                    $priceRange['$gte'] = (float) $filters['price_min'];
                }
                if (!empty($filters['price_max'])) {
                    $priceRange['$lte'] = (float) $filters['price_max'];
                }
                $matchConditions['price.base'] = $priceRange;
            }

            if (!empty($filters['rating_min'])) {
                $matchConditions['reviews_summary.average_rating'] = [
                    '$gte' => (float) $filters['rating_min']
                ];
            }

            $pipeline[] = ['$match' => $matchConditions];

            // Faceted search for filters
            $pipeline[] = [
                '$facet' => [
                    'products' => [
                        [
                            '$sort' => !empty($searchTerm) 
                                ? ['relevance_score' => -1, 'reviews_summary.average_rating' => -1]
                                : ['reviews_summary.average_rating' => -1, 'metadata.purchase_count' => -1]
                        ],
                        ['$skip' => $filters['skip'] ?? 0],
                        ['$limit' => $filters['limit'] ?? 20]
                    ],
                    'facets' => [
                        [
                            '$group' => [
                                '_id' => null,
                                'categories' => [
                                    '$addToSet' => [
                                        'name' => '$category.name',
                                        'hierarchy' => '$category.hierarchy.0'
                                    ]
                                ],
                                'price_range' => [
                                    '$push' => '$price.base'
                                ],
                                'brands' => [
                                    '$addToSet' => '$metadata.brand'
                                ],
                                'avg_rating' => [
                                    '$avg' => '$reviews_summary.average_rating'
                                ],
                                'total_results' => ['$sum' => 1]
                            ]
                        ],
                        [
                            '$project' => [
                                'categories' => 1,
                                'brands' => 1,
                                'price_range' => [
                                    'min' => ['$min' => '$price_range'],
                                    'max' => ['$max' => '$price_range']
                                ],
                                'avg_rating' => ['$round' => ['$avg_rating', 1]],
                                'total_results' => 1
                            ]
                        ]
                    ]
                ]
            ];

            return $collection->aggregate($pipeline);
        });
    }

    // Query Scopes
    public function scopeActive($query)
    {
        return $query->where('metadata.status', 'active');
    }

    public function scopeInStock($query)
    {
        return $query->where('variants.inventory.available', '>', 0);
    }

    public function scopeByCategory($query, $category)
    {
        return $query->where('category.hierarchy', 'all', [$category]);
    }

    public function scopeFeatured($query)
    {
        return $query->where('metadata.is_featured', true);
    }

    public function scopeOnSale($query)
    {
        return $query->where('price.discount.valid_until', '>=', new \MongoDB\BSON\UTCDateTime());
    }

    // Helper Methods
    public function getCurrentPrice()
    {
        $basePrice = $this->price['base'] ?? 0;
        
        if (isset($this->price['discount']) && 
            isset($this->price['discount']['valid_until']) &&
            $this->price['discount']['valid_until'] >= now()) {
            
            $discount = $this->price['discount']['value'] ?? 0;
            return $basePrice - ($basePrice * ($discount / 100));
        }
        
        return $basePrice;
    }

    public function getTotalStock()
    {
        return collect($this->variants)->sum('inventory.quantity');
    }

    public function getAvailableStock()
    {
        return collect($this->variants)->sum('inventory.available');
    }

    public function isInStock()
    {
        return $this->getAvailableStock() > 0;
    }

    public function getVariantBySku($sku)
    {
        return collect($this->variants)->firstWhere('sku', $sku);
    }

    // Relationships (using references)
    public function reviews()
    {
        return $this->hasMany(Review::class, 'product_id');
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class, 'product_id');
    }

    public function cartItems()
    {
        return $this->hasMany(CartItem::class, 'product_id');
    }
}