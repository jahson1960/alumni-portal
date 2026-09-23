-- Assigns placeholder avatar illustrations to the seeded alumni that don't have one yet.
-- Illustrated (DiceBear avataaars, dark skin tones) rather than stock photos, since free
-- photo-placeholder services do not offer reliable ethnicity filtering.
-- Image files live in assets/uploads/avatars/ alongside this repo copy.
-- Safe to re-run: only touches rows whose avatar is still empty.
-- Run against an already-selected database (phpMyAdmin) or on the command line
-- as `mysql ... your_db_name < seed_avatars.sql` - no USE/CREATE DATABASE here,
-- since most hosts don't grant the app's DB user that permission.

UPDATE users SET avatar = 'avatars/seed_alumni_3.png' WHERE email = 'amaka.eze@example.com' AND (avatar IS NULL OR avatar = '');
UPDATE users SET avatar = 'avatars/seed_alumni_4.png' WHERE email = 'tunde.bakare@example.com' AND (avatar IS NULL OR avatar = '');
UPDATE users SET avatar = 'avatars/seed_alumni_7.png' WHERE email = 'ngozi.adeyemi@example.com' AND (avatar IS NULL OR avatar = '');
UPDATE users SET avatar = 'avatars/seed_alumni_8.png' WHERE email = 'emeka.nwosu@example.com' AND (avatar IS NULL OR avatar = '');
UPDATE users SET avatar = 'avatars/seed_alumni_9.png' WHERE email = 'folake.ogundimu@example.com' AND (avatar IS NULL OR avatar = '');
UPDATE users SET avatar = 'avatars/seed_alumni_10.png' WHERE email = 'ibrahim.suleiman@example.com' AND (avatar IS NULL OR avatar = '');
UPDATE users SET avatar = 'avatars/seed_alumni_11.png' WHERE email = 'chiamaka.umeh@example.com' AND (avatar IS NULL OR avatar = '');
UPDATE users SET avatar = 'avatars/seed_alumni_12.png' WHERE email = 'segun.afolabi@example.com' AND (avatar IS NULL OR avatar = '');
UPDATE users SET avatar = 'avatars/seed_alumni_13.png' WHERE email = 'blessing.etim@example.com' AND (avatar IS NULL OR avatar = '');
UPDATE users SET avatar = 'avatars/seed_alumni_14.png' WHERE email = 'yusuf.abdullahi@example.com' AND (avatar IS NULL OR avatar = '');
UPDATE users SET avatar = 'avatars/seed_alumni_15.png' WHERE email = 'adaeze.chukwu@example.com' AND (avatar IS NULL OR avatar = '');
UPDATE users SET avatar = 'avatars/seed_alumni_16.png' WHERE email = 'kemi.ajayi@example.com' AND (avatar IS NULL OR avatar = '');
UPDATE users SET avatar = 'avatars/seed_alumni_17.png' WHERE email = 'obinna.eze@example.com' AND (avatar IS NULL OR avatar = '');
UPDATE users SET avatar = 'avatars/seed_alumni_18.png' WHERE email = 'halima.bello@example.com' AND (avatar IS NULL OR avatar = '');
UPDATE users SET avatar = 'avatars/seed_alumni_19.png' WHERE email = 'tobi.alabi@example.com' AND (avatar IS NULL OR avatar = '');
UPDATE users SET avatar = 'avatars/seed_alumni_20.png' WHERE email = 'chinwe.okafor@example.com' AND (avatar IS NULL OR avatar = '');
UPDATE users SET avatar = 'avatars/seed_alumni_21.png' WHERE email = 'musa.aliyu@example.com' AND (avatar IS NULL OR avatar = '');
UPDATE users SET avatar = 'avatars/seed_alumni_22.png' WHERE email = 'funmilayo.salako@example.com' AND (avatar IS NULL OR avatar = '');
UPDATE users SET avatar = 'avatars/seed_alumni_23.png' WHERE email = 'david.okeke@example.com' AND (avatar IS NULL OR avatar = '');
UPDATE users SET avatar = 'avatars/seed_alumni_24.png' WHERE email = 'ronke.bamidele@example.com' AND (avatar IS NULL OR avatar = '');
UPDATE users SET avatar = 'avatars/seed_alumni_25.png' WHERE email = 'uche.iwu@example.com' AND (avatar IS NULL OR avatar = '');
UPDATE users SET avatar = 'avatars/seed_alumni_26.png' WHERE email = 'grace.effiong@example.com' AND (avatar IS NULL OR avatar = '');
UPDATE users SET avatar = 'avatars/seed_alumni_27.png' WHERE email = 'yewande.adisa@example.com' AND (avatar IS NULL OR avatar = '');
UPDATE users SET avatar = 'avatars/seed_alumni_28.png' WHERE email = 'kelechi.nnamdi@example.com' AND (avatar IS NULL OR avatar = '');
UPDATE users SET avatar = 'avatars/seed_alumni_29.png' WHERE email = 'aisha.mohammed@example.com' AND (avatar IS NULL OR avatar = '');
UPDATE users SET avatar = 'avatars/seed_alumni_30.png' WHERE email = 'femi.adewale@example.com' AND (avatar IS NULL OR avatar = '');
UPDATE users SET avatar = 'avatars/seed_alumni_31.png' WHERE email = 'ngozi.obiora@example.com' AND (avatar IS NULL OR avatar = '');
UPDATE users SET avatar = 'avatars/seed_alumni_32.png' WHERE email = 'babajide.fashola@example.com' AND (avatar IS NULL OR avatar = '');
UPDATE users SET avatar = 'avatars/seed_alumni_33.png' WHERE email = 'chidinma.uzo@example.com' AND (avatar IS NULL OR avatar = '');
UPDATE users SET avatar = 'avatars/seed_alumni_34.png' WHERE email = 'suleiman.bello@example.com' AND (avatar IS NULL OR avatar = '');
UPDATE users SET avatar = 'avatars/seed_alumni_35.png' WHERE email = 'temitope.adeyemi@example.com' AND (avatar IS NULL OR avatar = '');
UPDATE users SET avatar = 'avatars/seed_alumni_36.png' WHERE email = 'ikechukwu.okoro@example.com' AND (avatar IS NULL OR avatar = '');
UPDATE users SET avatar = 'avatars/seed_alumni_37.png' WHERE email = 'zainab.yusuf@example.com' AND (avatar IS NULL OR avatar = '');
UPDATE users SET avatar = 'avatars/seed_alumni_38.png' WHERE email = 'olumide.fagbenle@example.com' AND (avatar IS NULL OR avatar = '');
UPDATE users SET avatar = 'avatars/seed_alumni_39.png' WHERE email = 'chizoba.nwachukwu@example.com' AND (avatar IS NULL OR avatar = '');
UPDATE users SET avatar = 'avatars/seed_alumni_40.png' WHERE email = 'hauwa.garba@example.com' AND (avatar IS NULL OR avatar = '');
UPDATE users SET avatar = 'avatars/seed_alumni_41.png' WHERE email = 'emmanuel.etuk@example.com' AND (avatar IS NULL OR avatar = '');
UPDATE users SET avatar = 'avatars/seed_alumni_42.png' WHERE email = 'fatima.sani@example.com' AND (avatar IS NULL OR avatar = '');
UPDATE users SET avatar = 'avatars/seed_alumni_43.png' WHERE email = 'adebayo.ogunleye@example.com' AND (avatar IS NULL OR avatar = '');
UPDATE users SET avatar = 'avatars/seed_alumni_44.png' WHERE email = 'nkechi.chukwuemeka@example.com' AND (avatar IS NULL OR avatar = '');
UPDATE users SET avatar = 'avatars/seed_alumni_45.png' WHERE email = 'rasheed.balogun@example.com' AND (avatar IS NULL OR avatar = '');
UPDATE users SET avatar = 'avatars/seed_alumni_46.png' WHERE email = 'omolara.ajibade@example.com' AND (avatar IS NULL OR avatar = '');
UPDATE users SET avatar = 'avatars/seed_alumni_47.png' WHERE email = 'chukwuemeka.eze@example.com' AND (avatar IS NULL OR avatar = '');
UPDATE users SET avatar = 'avatars/seed_alumni_48.png' WHERE email = 'amina.lawal@example.com' AND (avatar IS NULL OR avatar = '');
UPDATE users SET avatar = 'avatars/seed_alumni_49.png' WHERE email = 'tunji.alabi@example.com' AND (avatar IS NULL OR avatar = '');
UPDATE users SET avatar = 'avatars/seed_alumni_50.png' WHERE email = 'vivian.okonjo@example.com' AND (avatar IS NULL OR avatar = '');
UPDATE users SET avatar = 'avatars/seed_alumni_51.png' WHERE email = 'musa.danjuma@example.com' AND (avatar IS NULL OR avatar = '');
UPDATE users SET avatar = 'avatars/seed_alumni_52.png' WHERE email = 'bisi.adeoye@example.com' AND (avatar IS NULL OR avatar = '');
UPDATE users SET avatar = 'avatars/seed_alumni_53.png' WHERE email = 'chinedu.obi@example.com' AND (avatar IS NULL OR avatar = '');
UPDATE users SET avatar = 'avatars/seed_alumni_54.png' WHERE email = 'rukayat.bakare@example.com' AND (avatar IS NULL OR avatar = '');
UPDATE users SET avatar = 'avatars/seed_alumni_55.png' WHERE email = 'godwin.osei@example.com' AND (avatar IS NULL OR avatar = '');
UPDATE users SET avatar = 'avatars/seed_alumni_56.png' WHERE email = 'precious.mensah@example.com' AND (avatar IS NULL OR avatar = '');
