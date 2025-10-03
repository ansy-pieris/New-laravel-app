<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use MongoDB\Client;

class MongoAdvancedController extends Controller
{
    private $mongodb;
    
    public function __construct()
    {
        // Direct MongoDB connection for advanced NoSQL features
        $this->mongodb = new Client(config('database.connections.mongodb.dsn'));
    }

    /**
     * Advanced NoSQL Operations Demo
     */
    public function advancedNoSQLDemo(Request $request)
    {
        $collection = $this->mongodb->apparel_store->products;
        
        // Advanced MongoDB aggregation pipeline
        $pipeline = [
            ['$group' => [
                '_id' => '$category_id',
                'total_products' => ['$sum' => 1],
                'avg_price' => ['$avg' => '$price']
            ]],
            ['$sort' => ['total_products' => -1]]
        ];
        
        $results = $collection->aggregate($pipeline)->toArray();
        
        return response()->json([
            'message' => 'Advanced NoSQL operations demonstrated',
            'user' => $request->user()->email,
            'nosql_features' => [
                'aggregation_pipeline' => 'GROUP BY and AVG operations',
                'document_queries' => 'Complex filtering and sorting',
                'schema_flexibility' => 'Dynamic field handling'
            ],
            'category_analytics' => $results
        ]);
    }

    /**
     * Real-time NoSQL Document Creation
     */
    public function createDocument(Request $request)
    {
        $collection = $this->mongodb->apparel_store->university_demo;
        
        $document = [
            'student_submission' => [
                'email' => $request->user()->email,
                'timestamp' => new \MongoDB\BSON\UTCDateTime(),
                'assignment_type' => 'Laravel Sanctum + MongoDB',
                'features_implemented' => [
                    'authentication' => 'Sanctum Token-based',
                    'database' => 'MongoDB Atlas NoSQL',
                    'api_security' => 'Bearer Token Protection'
                ]
            ]
        ];
        
        $result = $collection->insertOne($document);
        
        return response()->json([
            'message' => 'Document created in MongoDB',
            'document_id' => $result->getInsertedId(),
            'nosql_proof' => 'Real document insertion completed'
        ]);
    }
}