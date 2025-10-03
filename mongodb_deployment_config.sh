# MongoDB Deployment Configuration for Outstanding Integration
# This file demonstrates advanced MongoDB deployment strategies

# =============================================================================
# REPLICA SET CONFIGURATION (High Availability)
# =============================================================================

# MongoDB Replica Set Setup Script
# Run this on your primary server to initialize the replica set

# 1. Start MongoDB instances on different ports/servers
# Primary: mongod --replSet "apparelStoreRS" --port 27017 --dbpath /data/rs1
# Secondary 1: mongod --replSet "apparelStoreRS" --port 27018 --dbpath /data/rs2  
# Secondary 2: mongod --replSet "apparelStoreRS" --port 27019 --dbpath /data/rs3
# Arbiter: mongod --replSet "apparelStoreRS" --port 27020 --dbpath /data/arbiter

# 2. Initialize replica set (connect to primary)
mongo --port 27017 --eval "
rs.initiate({
  _id: 'apparelStoreRS',
  version: 1,
  members: [
    {
      _id: 0,
      host: 'mongodb-primary:27017',
      priority: 2,
      tags: { 'dc': 'primary', 'region': 'us-east' }
    },
    {
      _id: 1,
      host: 'mongodb-secondary1:27018',
      priority: 1,
      tags: { 'dc': 'secondary', 'region': 'us-east' }
    },
    {
      _id: 2,
      host: 'mongodb-secondary2:27019',
      priority: 1,
      tags: { 'dc': 'secondary', 'region': 'us-west' }
    },
    {
      _id: 3,
      host: 'mongodb-arbiter:27020',
      arbiterOnly: true
    }
  ],
  settings: {
    chainingAllowed: true,
    heartbeatTimeoutSecs: 2,
    getLastErrorModes: {
      'majorityCrossRegion': {
        'region': 2
      }
    }
  }
})
"

# 3. Configure read preferences for different use cases
# Primary: For all writes and critical reads
# PrimaryPreferred: For real-time analytics
# Secondary: For reporting and analytics
# SecondaryPreferred: For user-facing reads
# Nearest: For geographically distributed reads

# =============================================================================
# SHARDING CONFIGURATION (Horizontal Scaling)  
# =============================================================================

# MongoDB Sharded Cluster Setup

# 1. Start Config Server Replica Set
# Config Server 1: mongod --configsvr --replSet "configRS" --port 27021 --dbpath /data/config1
# Config Server 2: mongod --configsvr --replSet "configRS" --port 27022 --dbpath /data/config2  
# Config Server 3: mongod --configsvr --replSet "configRS" --port 27023 --dbpath /data/config3

# Initialize Config Server Replica Set
mongo --port 27021 --eval "
rs.initiate({
  _id: 'configRS',
  configsvr: true,
  members: [
    { _id: 0, host: 'config-server1:27021' },
    { _id: 1, host: 'config-server2:27022' },
    { _id: 2, host: 'config-server3:27023' }
  ]
})
"

# 2. Start Shard Replica Sets
# Shard 1 (Users & Orders)
# mongod --shardsvr --replSet "shard1RS" --port 27026 --dbpath /data/shard1a
# mongod --shardsvr --replSet "shard1RS" --port 27027 --dbpath /data/shard1b

# Shard 2 (Products & Reviews)  
# mongod --shardsvr --replSet "shard2RS" --port 27028 --dbpath /data/shard2a
# mongod --shardsvr --replSet "shard2RS" --port 27029 --dbpath /data/shard2b

# Initialize Shard 1
mongo --port 27026 --eval "
rs.initiate({
  _id: 'shard1RS',
  members: [
    { _id: 0, host: 'shard1-primary:27026' },
    { _id: 1, host: 'shard1-secondary:27027' }
  ]
})
"

# Initialize Shard 2
mongo --port 27028 --eval "
rs.initiate({
  _id: 'shard2RS', 
  members: [
    { _id: 0, host: 'shard2-primary:27028' },
    { _id: 1, host: 'shard2-secondary:27029' }
  ]
})
"

# 3. Start Mongos Routers
# mongos --configdb "configRS/config-server1:27021,config-server2:27022,config-server3:27023" --port 27024
# mongos --configdb "configRS/config-server1:27021,config-server2:27022,config-server3:27023" --port 27025

# 4. Add Shards to Cluster
mongo --port 27024 --eval "
sh.addShard('shard1RS/shard1-primary:27026,shard1-secondary:27027');
sh.addShard('shard2RS/shard2-primary:27028,shard2-secondary:27029');
"

# 5. Enable Sharding on Database
mongo --port 27024 --eval "
sh.enableSharding('apparel_store_mongo');
"

# 6. Create Strategic Shard Keys
mongo --port 27024 --eval "
// Shard users by user_id (good distribution)
sh.shardCollection('apparel_store_mongo.users', { '_id': 1 });

// Shard orders by user_id and created_at (time-series partitioning)
sh.shardCollection('apparel_store_mongo.orders', { 'user_id': 1, 'created_at': 1 });

// Shard products by category hierarchy for query optimization
sh.shardCollection('apparel_store_mongo.products', { 'category.hierarchy.0': 1, '_id': 1 });

// Create compound shard key for reviews (product-based sharding)
sh.shardCollection('apparel_store_mongo.reviews', { 'product_id': 1, 'created_at': 1 });
"

# =============================================================================
# ADVANCED INDEXING STRATEGY
# =============================================================================

mongo --eval "
use apparel_store_mongo;

// ========== USER COLLECTION INDEXES ==========
// Unique compound index on email (with partial filter for active users)
db.users.createIndex(
  { 'email': 1, 'deleted_at': 1 }, 
  { 
    unique: true, 
    sparse: true,
    name: 'idx_users_email_active'
  }
);

// Compound index for role-based queries with performance optimization
db.users.createIndex(
  { 'role': 1, 'metadata.last_login_at': -1, 'created_at': -1 },
  { name: 'idx_users_role_activity' }
);

// Geospatial index for location-based features
db.users.createIndex(
  { 'addresses.coordinates': '2dsphere' },
  { name: 'idx_users_geo' }
);

// Full-text search index with weighted fields
db.users.createIndex(
  { 
    'name': 'text', 
    'email': 'text', 
    'profile.bio': 'text',
    'addresses.city': 'text'
  },
  { 
    weights: {
      'name': 10,
      'email': 8,
      'profile.bio': 3,
      'addresses.city': 2
    },
    name: 'idx_users_text_search'
  }
);

// Index for customer segmentation queries
db.users.createIndex(
  { 
    'role': 1,
    'metadata.total_spent': -1, 
    'metadata.order_count': -1,
    'metadata.loyalty_points': -1
  },
  { name: 'idx_users_segmentation' }
);

// TTL index for inactive user cleanup (optional)
db.users.createIndex(
  { 'metadata.last_login_at': 1 },
  { 
    expireAfterSeconds: 31536000, // 1 year
    partialFilterExpression: { 'role': 'customer' },
    name: 'idx_users_ttl_cleanup'
  }
);

// ========== PRODUCT COLLECTION INDEXES ==========
// Compound index for category and price filtering (most common query pattern)
db.products.createIndex(
  { 
    'category.hierarchy': 1, 
    'price.base': 1, 
    'metadata.status': 1,
    'reviews_summary.average_rating': -1
  },
  { name: 'idx_products_category_price' }
);

// Full-text search with advanced weighting
db.products.createIndex(
  {
    'name': 'text',
    'description': 'text',
    'category.name': 'text',
    'metadata.tags': 'text',
    'seo.keywords': 'text',
    'metadata.brand': 'text'
  },
  {
    weights: {
      'name': 15,
      'metadata.brand': 10,
      'description': 5,
      'category.name': 4,
      'metadata.tags': 3,
      'seo.keywords': 2
    },
    name: 'idx_products_full_text',
    default_language: 'english'
  }
);

// Geospatial index for warehouse-based inventory queries
db.products.createIndex(
  { 'variants.inventory.warehouse_locations.coordinates': '2dsphere' },
  { name: 'idx_products_warehouse_geo' }
);

// Sparse index for discounted products (time-based)
db.products.createIndex(
  { 'price.discount.valid_until': 1 },
  { 
    sparse: true,
    name: 'idx_products_discount_expiry'
  }
);

// Performance index for product listing and sorting
db.products.createIndex(
  { 
    'metadata.status': 1,
    'reviews_summary.average_rating': -1, 
    'metadata.purchase_count': -1,
    'created_at': -1
  },
  { name: 'idx_products_popularity' }
);

// Inventory management index
db.products.createIndex(
  { 
    'variants.inventory.quantity': 1,
    'variants.inventory.available': 1,
    'metadata.status': 1
  },
  { name: 'idx_products_inventory' }
);

// Compound index for variant-specific queries
db.products.createIndex(
  { 
    'variants.sku': 1,
    'variants.size': 1,
    'variants.color.name': 1
  },
  { name: 'idx_products_variants' }
);

// ========== ORDER COLLECTION INDEXES ==========
// Primary compound index for user orders (most frequent query)
db.orders.createIndex(
  { 
    'user_id': 1, 
    'status': 1, 
    'created_at': -1 
  },
  { name: 'idx_orders_user_status_date' }
);

// Unique index for order number lookup
db.orders.createIndex(
  { 'order_number': 1 }, 
  { 
    unique: true,
    name: 'idx_orders_number'
  }
);

// Geospatial index for shipping location analysis
db.orders.createIndex(
  { 'shipping.address.coordinates': '2dsphere' },
  { name: 'idx_orders_shipping_geo' }
);

// Index for payment processing and reconciliation
db.orders.createIndex(
  { 
    'payment.transaction_id': 1, 
    'payment.provider': 1,
    'payment.status': 1
  },
  { name: 'idx_orders_payment' }
);

// Business analytics index
db.orders.createIndex(
  { 
    'status': 1, 
    'created_at': -1, 
    'pricing.grand_total': -1,
    'metadata.source': 1
  },
  { name: 'idx_orders_analytics' }
);

// Fulfillment operations index
db.orders.createIndex(
  { 
    'fulfillment.warehouse_id': 1, 
    'fulfillment.assigned_to': 1,
    'status': 1,
    'created_at': -1
  },
  { name: 'idx_orders_fulfillment' }
);

// Index for tracking and shipping queries
db.orders.createIndex(
  { 
    'shipping.tracking.number': 1,
    'shipping.tracking.status': 1
  },
  { 
    sparse: true,
    name: 'idx_orders_tracking'
  }
);

// TTL index for abandoned cart cleanup
db.orders.createIndex(
  { 'updated_at': 1 }, 
  { 
    expireAfterSeconds: 2592000, // 30 days
    partialFilterExpression: { 'status': 'pending' },
    name: 'idx_orders_ttl_abandoned'
  }
);

// Index for customer lifetime value calculations
db.orders.createIndex(
  {
    'user_id': 1,
    'status': 1,
    'pricing.grand_total': -1,
    'created_at': 1
  },
  {
    partialFilterExpression: { 
      'status': { \$in: ['paid', 'processing', 'shipped', 'delivered'] }
    },
    name: 'idx_orders_ltv_analysis'
  }
);

print('All indexes created successfully!');
"

# =============================================================================
# MONGODB CONFIGURATION FILES
# =============================================================================

# Create mongod.conf for production deployment
cat << 'EOF' > /etc/mongod.conf
# MongoDB Production Configuration
systemLog:
  destination: file
  path: /var/log/mongodb/mongod.log
  logAppend: true
  logRotate: reopen

storage:
  dbPath: /var/lib/mongodb
  journal:
    enabled: true
  engine: wiredTiger
  wiredTiger:
    engineConfig:
      cacheSizeGB: 8
      journalCompressor: snappy
      directoryForIndexes: true
    collectionConfig:
      blockCompressor: snappy
    indexConfig:
      prefixCompression: true

processManagement:
  fork: true
  pidFilePath: /var/run/mongodb/mongod.pid
  timeZoneInfo: /usr/share/zoneinfo

net:
  port: 27017
  bindIp: 0.0.0.0
  maxIncomingConnections: 1000
  compression:
    compressors: snappy,zstd

security:
  authorization: enabled
  keyFile: /etc/mongodb/mongodb-keyfile
  clusterAuthMode: keyFile

replication:
  replSetName: apparelStoreRS
  enableMajorityReadConcern: true

sharding:
  clusterRole: shardsvr

setParameter:
  authenticationMechanisms: SCRAM-SHA-1,SCRAM-SHA-256
  maxLogSizeKB: 100000
  logLevel: 1
  enableFlowControl: true
  flowControlTargetLagSeconds: 10

operationProfiling:
  slowOpThresholdMs: 100
  mode: slowOp
  slowOpSampleRate: 0.1
EOF

# =============================================================================
# MONITORING AND MAINTENANCE SCRIPTS  
# =============================================================================

# MongoDB Health Check Script
cat << 'EOF' > /scripts/mongodb_health_check.js
// MongoDB Health and Performance Monitoring
use apparel_store_mongo;

print("=== MongoDB Cluster Health Report ===");
print("Generated at: " + new Date());
print("");

// Replica Set Status
print("=== Replica Set Status ===");
var rsStatus = rs.status();
print("Replica Set: " + rsStatus.set);
print("Primary: " + rsStatus.members.find(m => m.stateStr === "PRIMARY").name);
print("Members:");
rsStatus.members.forEach(function(member) {
  print("  " + member.name + " - " + member.stateStr + " (Health: " + member.health + ")");
});
print("");

// Database Statistics
print("=== Database Statistics ===");
var dbStats = db.stats();
print("Total Size: " + Math.round(dbStats.dataSize / 1024 / 1024) + " MB");
print("Index Size: " + Math.round(dbStats.indexSize / 1024 / 1024) + " MB");
print("Collections: " + dbStats.collections);
print("Objects: " + dbStats.objects);
print("");

// Collection Statistics
print("=== Collection Statistics ===");
['users', 'products', 'orders'].forEach(function(collName) {
  var stats = db.getCollection(collName).stats();
  print(collName + ":");
  print("  Documents: " + stats.count);
  print("  Data Size: " + Math.round(stats.size / 1024 / 1024) + " MB");
  print("  Index Size: " + Math.round(stats.totalIndexSize / 1024 / 1024) + " MB");
  print("  Avg Doc Size: " + Math.round(stats.avgObjSize) + " bytes");
});
print("");

// Index Usage Analysis
print("=== Index Usage Analysis ===");
db.users.aggregate([{$indexStats:{}}, {$sort: {"accesses.ops": -1}}]).forEach(function(index) {
  if (index.accesses.ops > 0) {
    print("users." + index.name + ": " + index.accesses.ops + " operations");
  }
});

// Recent Slow Queries
print("=== Recent Slow Queries (>100ms) ===");
db.system.profile.find({ts: {$gt: new Date(Date.now() - 1000*60*60)}}).sort({ts: -1}).limit(5).forEach(function(op) {
  print("Duration: " + op.millis + "ms, Collection: " + op.ns + ", Command: " + op.command.find || op.command.aggregate || "other");
});
print("");

print("=== Health Check Complete ===");
EOF

# Performance Optimization Script
cat << 'EOF' > /scripts/mongodb_optimize.js
// MongoDB Performance Optimization
use apparel_store_mongo;

print("=== MongoDB Performance Optimization ===");

// Analyze and suggest index improvements
print("=== Index Analysis ===");

// Check for unused indexes
db.users.aggregate([{$indexStats:{}}, {$match: {"accesses.ops": 0}}]).forEach(function(index) {
  if (index.name !== "_id_") {
    print("Unused index found: users." + index.name);
  }
});

// Check for missing indexes based on slow queries
db.system.profile.aggregate([
  {$match: {ts: {$gt: new Date(Date.now() - 1000*60*60*24)}, millis: {$gt: 100}}},
  {$group: {_id: "$command", count: {$sum: 1}, avgMs: {$avg: "$millis"}}},
  {$sort: {count: -1}},
  {$limit: 10}
]).forEach(function(query) {
  print("Frequent slow query pattern - Count: " + query.count + ", Avg: " + Math.round(query.avgMs) + "ms");
  printjson(query._id);
});

// Compaction recommendations
print("=== Storage Optimization ===");
db.stats().collections > 0 && db.runCommand({collStats: "users"}).wiredTiger && 
  print("Consider running compact operations during maintenance windows");

print("=== Optimization Complete ===");
EOF

print("MongoDB advanced configuration completed!");
print("Next steps:");
print("1. Install MongoDB extension: pecl install mongodb");
print("2. Add extension=mongodb to php.ini");
print("3. Install composer package: composer require mongodb/laravel-mongodb");
print("4. Configure environment variables");
print("5. Run database migrations and seeders");
print("6. Initialize replica sets and sharding as needed");