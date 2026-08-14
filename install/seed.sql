SET NAMES utf8mb4;
START TRANSACTION;
INSERT INTO clubs (id,name,address,phone,tv_token,loyalty_points_rate,loyalty_redemption_rate,currency_symbol) VALUES (1,'Royal Snooker Club','Main Boulevard, Gulberg, Lahore','0300-1234567','4bfc5ee2e9974c0bba91ba7d048fa9f2',10,1,'Rs.');
INSERT INTO users (id,club_id,name,email,password,role,is_active) VALUES
(1,1,'Hassan Ahmed','admin@demo.com','$2y$10$C2.ORv8C/vsezZphA0KEP.KbdOaLNq/8iXJjktbM8nwY6EPm7GS2S','owner',1),
(2,1,'Ayesha Malik','manager@demo.com','$2y$10$C2.ORv8C/vsezZphA0KEP.KbdOaLNq/8iXJjktbM8nwY6EPm7GS2S','manager',1),
(3,1,'Muhammad Ali','cashier1@demo.com','$2y$10$C2.ORv8C/vsezZphA0KEP.KbdOaLNq/8iXJjktbM8nwY6EPm7GS2S','cashier',1),
(4,1,'Usman Raza','cashier2@demo.com','$2y$10$C2.ORv8C/vsezZphA0KEP.KbdOaLNq/8iXJjktbM8nwY6EPm7GS2S','cashier',1);
INSERT INTO rate_plans (id,club_id,plan_name,billing_type,rate_per_minute,rate_per_frame,slab_config) VALUES
(1,1,'Standard','per_minute',10,0,NULL),(2,1,'Premium','per_minute',15,0,NULL),(3,1,'Weekend Slab','slab',0,0,'[{"up_to_minutes":15,"price":200},{"up_to_minutes":60,"price":700},{"per_extra_minutes":15,"extra_price":100}]'),(4,1,'Frame Match','per_frame',0,180,NULL);
INSERT INTO tables (id,club_id,table_number,table_name,table_type,status,rate_plan_id) VALUES
(1,1,'1','Table 1','snooker','available',1),(2,1,'2','Table 2','snooker','available',1),(3,1,'3','Table 3','snooker','available',2),(4,1,'4','Table 4','snooker','available',2),(5,1,'5','Table 5','snooker','available',1),(6,1,'6','Table 6','snooker','available',3),(7,1,'7','Pool Table 1','pool','available',1),(8,1,'8','Pool Table 2','pool','available',4);
INSERT INTO players (id,club_id,name,phone,email,tier,loyalty_points,total_spent,total_visits,ledger_balance,joined_date,last_visit) VALUES
(1,1,'Ahmed Khan','0301-1111111','ahmed@example.com','gold',1840,18400,20,0,'2025-10-14',NOW()-INTERVAL 1 DAY),(2,1,'Bilal Shah','0302-2222222','bilal@example.com','silver',720,7200,11,0,'2025-12-05',NOW()-INTERVAL 2 DAY),(3,1,'Hamza Iqbal','0303-3333333','hamza@example.com','regular',210,2100,5,0,'2026-01-08',NOW()-INTERVAL 3 DAY),(4,1,'Ali Raza','0304-4444444','ali@example.com','platinum',5300,53000,42,0,'2025-06-12',NOW()-INTERVAL 5 DAY),(5,1,'Zain Abbas','0305-5555555','zain@example.com','gold',1630,16300,18,-450,'2025-08-02',NOW()-INTERVAL 6 DAY),(6,1,'Saad Ahmed','0306-6666666','saad@example.com','silver',650,6500,12,0,'2025-11-18',NOW()-INTERVAL 7 DAY),(7,1,'Danish Rauf','0307-7777777','danish@example.com','regular',300,3000,8,0,'2026-02-02',NOW()-INTERVAL 9 DAY),(8,1,'Fahad Qureshi','0308-8888888','fahad@example.com','gold',1770,17700,21,0,'2025-07-19',NOW()-INTERVAL 10 DAY),(9,1,'Usman Khalid','0309-9999999','usman@example.com','silver',820,8200,10,0,'2025-10-28',NOW()-INTERVAL 12 DAY),(10,1,'Taimur Javed','0310-1010101','taimur@example.com','regular',120,1200,3,0,'2026-03-01',NOW()-INTERVAL 14 DAY),(11,1,'Salman Akram','0311-1111111','salman@example.com','gold',1900,19000,26,0,'2025-07-01',NOW()-INTERVAL 16 DAY),(12,1,'Noman Tariq','0312-1212121','noman@example.com','regular',80,800,2,0,'2026-04-04',NOW()-INTERVAL 17 DAY),(13,1,'Ibrahim Zafar','0313-1313131','ibrahim@example.com','silver',930,9300,13,0,'2025-12-21',NOW()-INTERVAL 20 DAY),(14,1,'Talha Awan','0314-1414141','talha@example.com','regular',430,4300,7,0,'2026-01-31',NOW()-INTERVAL 21 DAY),(15,1,'Rayan Siddiq','0315-1515151','rayan@example.com','platinum',6200,62000,47,0,'2025-04-09',NOW()-INTERVAL 25 DAY);
INSERT INTO cafe_items (id,club_id,name,category,price,stock_quantity,low_stock_threshold) VALUES
(1,1,'Karak Chai','Drinks',30,80,10),(2,1,'Cold Drink','Drinks',80,50,8),(3,1,'Mineral Water','Drinks',50,65,10),(4,1,'Fresh Juice','Drinks',120,35,5),(5,1,'Samosa','Food',65,45,8),(6,1,'Club Sandwich','Food',220,25,5),(7,1,'French Fries','Food',180,32,5),(8,1,'Chips','Food',70,40,6),(9,1,'Chocolate','Other',100,30,5),(10,1,'Cue Chalk','Accessories',40,20,4);
INSERT INTO inventory_items (club_id,name,category,current_stock,unit,low_stock_threshold,cost_per_unit) VALUES
(1,'Chai Bags','cafe_stock',80,'bags',10,15),(1,'Cold Drink Bottles','cafe_stock',50,'bottles',8,55),(1,'Mineral Water','cafe_stock',65,'bottles',10,30),(1,'Snooker Chalk','equipment',2,'pieces',5,25),(1,'Cue Tips','equipment',5,'pieces',8,120),(1,'Cue Sticks','equipment',12,'pieces',3,1800);
INSERT INTO sessions (club_id,table_id,player_id,operator_id,start_time,end_time,total_pause_seconds,billing_type,rate_plan_id,frames_played,table_amount,cafe_amount,total_amount,discount_amount,points_used,points_earned,payment_method,status) VALUES
(1,1,1,3,NOW()-INTERVAL 1 DAY-INTERVAL 95 MINUTE,NOW()-INTERVAL 1 DAY,0,'per_minute',1,0,950,130,973,107,0,97,'cash','completed'),
(1,2,2,3,NOW()-INTERVAL 2 DAY-INTERVAL 60 MINUTE,NOW()-INTERVAL 2 DAY,0,'per_minute',1,0,600,80,646,34,0,64,'jazzcash','completed'),
(1,3,3,4,NOW()-INTERVAL 3 DAY-INTERVAL 83 MINUTE,NOW()-INTERVAL 3 DAY,0,'per_minute',2,0,1245,0,1245,0,0,124,'cash','completed'),
(1,4,4,3,NOW()-INTERVAL 4 DAY-INTERVAL 75 MINUTE,NOW()-INTERVAL 4 DAY,0,'per_minute',2,0,1125,220,1211,134,0,121,'cash','completed'),
(1,5,5,4,NOW()-INTERVAL 5 DAY-INTERVAL 45 MINUTE,NOW()-INTERVAL 5 DAY,0,'per_minute',1,0,450,180,567,63,0,56,'easypaisa','completed'),
(1,6,6,3,NOW()-INTERVAL 6 DAY-INTERVAL 75 MINUTE,NOW()-INTERVAL 6 DAY,0,'slab',3,0,800,0,760,40,0,76,'cash','completed'),
(1,7,7,4,NOW()-INTERVAL 7 DAY-INTERVAL 50 MINUTE,NOW()-INTERVAL 7 DAY,0,'per_minute',1,0,500,100,600,0,0,60,'cash','completed'),
(1,8,8,3,NOW()-INTERVAL 8 DAY-INTERVAL 3 HOUR,NOW()-INTERVAL 8 DAY,0,'per_frame',4,4,720,120,756,84,0,75,'jazzcash','completed'),
(1,1,9,4,NOW()-INTERVAL 9 DAY-INTERVAL 70 MINUTE,NOW()-INTERVAL 9 DAY,0,'per_minute',1,0,700,65,727,38,0,72,'cash','completed'),
(1,2,10,3,NOW()-INTERVAL 11 DAY-INTERVAL 48 MINUTE,NOW()-INTERVAL 11 DAY,0,'per_minute',1,0,480,0,480,0,0,48,'cash','completed'),
(1,3,11,4,NOW()-INTERVAL 13 DAY-INTERVAL 88 MINUTE,NOW()-INTERVAL 13 DAY,0,'per_minute',2,0,1320,80,1260,140,0,126,'easypaisa','completed'),
(1,4,12,3,NOW()-INTERVAL 15 DAY-INTERVAL 30 MINUTE,NOW()-INTERVAL 15 DAY,0,'per_minute',2,0,450,30,480,0,0,48,'cash','completed'),
(1,5,13,4,NOW()-INTERVAL 18 DAY-INTERVAL 90 MINUTE,NOW()-INTERVAL 18 DAY,0,'per_minute',1,0,900,120,969,51,0,96,'jazzcash','completed'),
(1,6,14,3,NOW()-INTERVAL 19 DAY-INTERVAL 60 MINUTE,NOW()-INTERVAL 19 DAY,0,'slab',3,0,700,0,700,0,0,70,'cash','completed'),
(1,7,15,4,NOW()-INTERVAL 22 DAY-INTERVAL 40 MINUTE,NOW()-INTERVAL 22 DAY,0,'per_minute',1,0,400,100,425,75,0,42,'cash','completed'),
(1,8,1,3,NOW()-INTERVAL 23 DAY-INTERVAL 2 HOUR,NOW()-INTERVAL 23 DAY,0,'per_frame',4,3,540,60,540,60,0,54,'easypaisa','completed'),
(1,1,2,4,NOW()-INTERVAL 24 DAY-INTERVAL 55 MINUTE,NOW()-INTERVAL 24 DAY,0,'per_minute',1,0,550,0,523,27,0,52,'cash','completed'),
(1,2,4,3,NOW()-INTERVAL 26 DAY-INTERVAL 100 MINUTE,NOW()-INTERVAL 26 DAY,0,'per_minute',1,0,1000,220,1098,122,0,109,'jazzcash','completed'),
(1,3,5,4,NOW()-INTERVAL 28 DAY-INTERVAL 72 MINUTE,NOW()-INTERVAL 28 DAY,0,'per_minute',2,0,1080,130,1089,121,0,108,'cash','completed'),
(1,4,6,3,NOW()-INTERVAL 29 DAY-INTERVAL 62 MINUTE,NOW()-INTERVAL 29 DAY,0,'per_minute',2,0,930,60,943,47,0,94,'cash','completed');
INSERT INTO cafe_orders (session_id,club_id,player_id,operator_id,total_amount,payment_method,status) SELECT id,club_id,player_id,operator_id,cafe_amount,payment_method,'completed' FROM sessions WHERE cafe_amount>0;
INSERT INTO expenses (club_id,category,amount,description,expense_date,added_by) VALUES
(1,'electricity',5000,'Monthly electricity bill',CURDATE()-INTERVAL 3 DAY,1),(1,'rent',25000,'Club rent',DATE_FORMAT(CURDATE(),'%Y-%m-01'),1),(1,'water',1200,'Water supply',CURDATE()-INTERVAL 8 DAY,2),(1,'maintenance',3500,'Table cloth repair',CURDATE()-INTERVAL 15 DAY,2),(1,'salaries',12000,'Staff advance',CURDATE()-INTERVAL 20 DAY,1);
INSERT INTO tournaments (id,club_id,name,sport,format,match_format,frames_count,entry_fee,prize_pool,prize_breakdown,status,created_by,started_at,completed_at) VALUES (1,1,'Lahore Weekend Championship','snooker','single_elim','best_of',5,500,8000,'{"first":5000,"second":2000,"third":1000}','completed',1,NOW()-INTERVAL 45 DAY,NOW()-INTERVAL 43 DAY);
INSERT INTO tournament_players (id,tournament_id,player_id,player_name,seed_number,entry_fee_paid,status) VALUES (1,1,1,'Ahmed Khan',1,1,'active'),(2,1,2,'Bilal Shah',2,1,'eliminated'),(3,1,4,'Ali Raza',3,1,'eliminated'),(4,1,5,'Zain Abbas',4,1,'eliminated');
INSERT INTO tournament_matches (tournament_id,round_number,match_number,player1_id,player2_id,player1_name,player2_name,player1_frames,player2_frames,winner_id,winner_name,status,played_at) VALUES (1,1,1,1,2,'Ahmed Khan','Bilal Shah',3,1,1,'Ahmed Khan','completed',NOW()-INTERVAL 44 DAY),(1,1,2,3,4,'Ali Raza','Zain Abbas',3,2,3,'Ali Raza','completed',NOW()-INTERVAL 44 DAY),(1,2,1,1,3,'Ahmed Khan','Ali Raza',3,2,1,'Ahmed Khan','completed',NOW()-INTERVAL 43 DAY);
INSERT INTO settings (club_id,setting_key,setting_value) VALUES (1,'tier_silver','500'),(1,'tier_gold','1500'),(1,'tier_platinum','5000'),(1,'tv_leaderboard_period','weekly');
INSERT INTO notifications (club_id,type,message) VALUES (1,'low_stock','Snooker Chalk is running low (2 pieces left).'),(1,'tier','Ahmed Khan upgraded to Gold tier!');
COMMIT;
