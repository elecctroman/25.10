INSERT INTO users (email, password_hash, name, role, status, created_at) VALUES
('admin@example.com', '$2y$12$IJm5ABSV1kvVB21mXCaOyuFEjcZh9kH0EKTT5F2COFG4wL23Ai3T6', 'Sistem Yöneticisi', 'admin', 'active', NOW()),
('personel@example.com', '$2y$12$aDmkPTcE2hlE/zgGA4oCVOAtwgf08K9RLu5yewG.V1EGoVvj5xDP6', 'Destek Personeli', 'staff', 'active', NOW()),
('musteri@example.com', '$2y$12$00fg783rKEMs2pmkYDi6fOkCQ54jKfxPkNyDH3WX7fVHYrvtHNj0.', 'Örnek Müşteri', 'customer', 'active', NOW());

INSERT INTO categories (parent_id, name, slug, status) VALUES
(NULL, 'Yazılım', 'yazilim', 'active'),
(1, 'Ofis Yazılımları', 'ofis-yazilimlari', 'active'),
(NULL, 'Oyun', 'oyun', 'active');

INSERT INTO products (category_id, type, sku, name, slug, description, image, is_featured, price, currency, tax_rate, status, created_at) VALUES
(1, 'license', 'WIN-OFFICE-2024', 'Office 2024 Pro', 'office-2024-pro', 'Dijital lisans anahtarı', NULL, 1, 899.90, 'TRY', 18, 'active', NOW()),
(3, 'epin', 'GAME-COIN-1000', '1000 Coin Paketi', '1000-coin-paketi', 'Oyun içi para', NULL, 1, 149.90, 'TRY', 18, 'active', NOW());

INSERT INTO product_variants (product_id, name, price_delta, sku) VALUES
(1, 'Tek Kullanıcı', 0, 'WIN-OFFICE-2024-1'),
(1, '3 Kullanıcı', 299.90, 'WIN-OFFICE-2024-3');

INSERT INTO license_keys (product_id, code, status, created_at) VALUES
(1, 'OFFICE-KEY-001', 'available', NOW()),
(1, 'OFFICE-KEY-002', 'available', NOW()),
(2, 'COIN-KEY-001', 'available', NOW());

INSERT INTO orders (order_no, customer_id, email, total_net, total_tax, total_gross, discount_total, currency, status, delivery_strategy, created_at, coupon_id) VALUES
('ORD20231001001', 3, 'musteri@example.com', 899.90, 161.98, 1061.88, 0, 'TRY', 'delivered', 'auto_key', DATE_SUB(NOW(), INTERVAL 2 DAY), NULL);

INSERT INTO order_items (order_id, product_id, variant_id, qty, unit_price, tax_rate, total) VALUES
(1, 1, 1, 1, 899.90, 18, 1061.88);

UPDATE license_keys SET status = 'sold', order_item_id = 1 WHERE code = 'OFFICE-KEY-001';

INSERT INTO deliveries (order_item_id, type, payload, delivered_at) VALUES
(1, 'code', 'OFFICE-KEY-001', DATE_SUB(NOW(), INTERVAL 2 DAY));

INSERT INTO coupons (code, type, value, min_total, max_discount, start_at, end_at, usage_limit, status) VALUES
('WELCOME10', 'percent', 10, 200, 100, DATE_SUB(NOW(), INTERVAL 30 DAY), DATE_ADD(NOW(), INTERVAL 30 DAY), 100, 'active');

INSERT INTO providers (name, `key`, status, config, created_at) VALUES
('Stub Provider', 'stub', 'active', '{"api_key":"stub-123"}', NOW());

INSERT INTO provider_events (provider_id, event_type, payload, status, created_at) VALUES
(1, 'balance_check', '{"balance":1234.56}', 'processed', NOW());

INSERT INTO audit_logs (user_id, action, entity, entity_id, context, ip, ua, created_at) VALUES
(1, 'seed.setup', 'system', NULL, '{"info":"Initial seed"}', '127.0.0.1', 'seed-script', NOW());
