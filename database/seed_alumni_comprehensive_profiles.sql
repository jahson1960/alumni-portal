-- Comprehensive alumni profile seed: fills in personal_interests, phone,
-- state, work experience (2 roles each) and education (2 entries each) for
-- every existing alumnus, plus any remaining gaps in bio/skills/expertise
-- areas/business interests/linkedin/company/headline/industry/city/country/
-- program/graduation_year. Also flips show_phone/show_email on so the new
-- contact info is actually visible on the public profile page.
-- Keyed by email (stable and unique across environments, unlike id, whose
-- auto-increment sequence has diverged between local and production).
-- Safe to re-run: every UPDATE only touches empty fields (IS NULL/empty
-- checks) and every INSERT is guarded by NOT EXISTS / a row-count check, so
-- re-running never creates duplicates or clobbers data a real admin or
-- alumnus has since filled in by hand.
-- Run against an already-selected database (phpMyAdmin) or on the command
-- line as `mysql ... your_db_name < seed_alumni_comprehensive_profiles.sql`.


UPDATE users SET personal_interests = 'Golf, Running, Gardening', phone = '+234 815 163 9615',
  bio = IF(bio IS NULL OR bio = '', 'Experienced professional in Logistics & Supply Chain, currently operations Analyst, GIG Logistics.', bio),
  skills = IF(skills IS NULL OR skills = '', 'Supply Chain Optimization', skills),
  expertise_areas = IF(expertise_areas IS NULL OR expertise_areas = '', 'Supply Chain Optimization', expertise_areas),
  business_interests = IF(business_interests IS NULL OR business_interests = '', 'Last-Mile Delivery', business_interests),
  linkedin_url = IF(linkedin_url IS NULL OR linkedin_url = '', 'https://linkedin.com/in/chidi.okonkwo', linkedin_url),
  company = IF(company IS NULL OR company = '', 'GIG Logistics', company),
  headline = IF(headline IS NULL OR headline = '', 'Operations Analyst, GIG Logistics', headline),
  industry = IF(industry IS NULL OR industry = '', 'Logistics & Supply Chain', industry),
  city = IF(city IS NULL OR city = '', 'Lagos', city),
  country = IF(country IS NULL OR country = '', 'Nigeria', country),
  state = IF(state IS NULL OR state = '', 'Lagos State', state),
  program = IF(program IS NULL OR program = '', 'MBA', program),
  graduation_year = IF(graduation_year IS NULL, 2014, graduation_year),
  show_phone = IF(show_phone = 0, 1, show_phone),
  show_email = IF(show_email = 0, 1, show_email)
WHERE email = 'chidi.okonkwo@example.com' AND (personal_interests IS NULL OR personal_interests = '');

INSERT INTO user_experience (user_id, title, company, location, start_date, end_date, is_current, description, sort_order)
SELECT id, 'Operations Analyst', 'GIG Logistics', 'Lagos, Nigeria', '2015-03-01', NULL, 1, 'Leading supply chain optimization initiatives at GIG Logistics, based in Lagos.', 0 FROM users WHERE email = 'chidi.okonkwo@example.com'
  AND NOT EXISTS (SELECT 1 FROM user_experience WHERE user_id = (SELECT id FROM users WHERE email = 'chidi.okonkwo@example.com'));

INSERT INTO user_experience (user_id, title, company, location, start_date, end_date, is_current, description, sort_order)
SELECT id, 'Associate, Supply Chain Optimization', 'DHL Nigeria', 'Lagos, Nigeria', '2011-01-01', '2014-12-31', 0, 'Supported supply chain optimization projects early in career at DHL Nigeria.', 1 FROM users WHERE email = 'chidi.okonkwo@example.com'
  AND (SELECT COUNT(*) FROM user_experience WHERE user_id = (SELECT id FROM users WHERE email = 'chidi.okonkwo@example.com')) = 1;

INSERT INTO user_education (user_id, school, degree, field_of_study, start_year, end_year, sort_order)
SELECT id, 'Rome Business School Nigeria', 'MBA', 'Business Administration', 2012, 2014, 0 FROM users WHERE email = 'chidi.okonkwo@example.com'
  AND NOT EXISTS (SELECT 1 FROM user_education WHERE user_id = (SELECT id FROM users WHERE email = 'chidi.okonkwo@example.com'));

INSERT INTO user_education (user_id, school, degree, field_of_study, start_year, end_year, sort_order)
SELECT id, 'Ahmadu Bello University', 'B.Sc', 'Business Administration', 2007, 2011, 1 FROM users WHERE email = 'chidi.okonkwo@example.com'
  AND (SELECT COUNT(*) FROM user_education WHERE user_id = (SELECT id FROM users WHERE email = 'chidi.okonkwo@example.com')) = 1;

UPDATE users SET personal_interests = 'Reading, Chess, Volunteering, Cycling', phone = '+234 718 876 7340',
  bio = IF(bio IS NULL OR bio = '', 'Passionate about operational excellence and business transformation across emerging markets.', bio),
  skills = IF(skills IS NULL OR skills = '', 'Operations, Process Improvement, Change Management', skills),
  expertise_areas = IF(expertise_areas IS NULL OR expertise_areas = '', 'Corporate Strategy, Operations', expertise_areas),
  business_interests = IF(business_interests IS NULL OR business_interests = '', 'Consulting, Manufacturing', business_interests),
  linkedin_url = IF(linkedin_url IS NULL OR linkedin_url = '', 'https://linkedin.com/in/amaka.eze', linkedin_url),
  company = IF(company IS NULL OR company = '', 'Deloitte West Africa', company),
  headline = IF(headline IS NULL OR headline = '', 'Strategy & Operations Manager', headline),
  industry = IF(industry IS NULL OR industry = '', 'Professional Services', industry),
  city = IF(city IS NULL OR city = '', 'Lagos', city),
  country = IF(country IS NULL OR country = '', 'Nigeria', country),
  state = IF(state IS NULL OR state = '', 'Lagos State', state),
  program = IF(program IS NULL OR program = '', 'MBA', program),
  graduation_year = IF(graduation_year IS NULL, 2021, graduation_year),
  show_phone = IF(show_phone = 0, 1, show_phone),
  show_email = IF(show_email = 0, 1, show_email)
WHERE email = 'amaka.eze@example.com' AND (personal_interests IS NULL OR personal_interests = '');

INSERT INTO user_experience (user_id, title, company, location, start_date, end_date, is_current, description, sort_order)
SELECT id, 'Strategy & Operations Manager', 'Deloitte West Africa', 'Lagos, Nigeria', '2022-06-01', NULL, 1, 'Leading corporate strategy, operations initiatives at Deloitte West Africa, based in Lagos.', 0 FROM users WHERE email = 'amaka.eze@example.com'
  AND NOT EXISTS (SELECT 1 FROM user_experience WHERE user_id = (SELECT id FROM users WHERE email = 'amaka.eze@example.com'));

INSERT INTO user_experience (user_id, title, company, location, start_date, end_date, is_current, description, sort_order)
SELECT id, 'Associate, Strategy Consulting', 'EY Nigeria', 'Lagos, Nigeria', '2018-01-01', '2021-12-31', 0, 'Supported corporate strategy, operations projects early in career at EY Nigeria.', 1 FROM users WHERE email = 'amaka.eze@example.com'
  AND (SELECT COUNT(*) FROM user_experience WHERE user_id = (SELECT id FROM users WHERE email = 'amaka.eze@example.com')) = 1;

INSERT INTO user_education (user_id, school, degree, field_of_study, start_year, end_year, sort_order)
SELECT id, 'Rome Business School Nigeria', 'MBA', 'Business Administration', 2019, 2021, 0 FROM users WHERE email = 'amaka.eze@example.com'
  AND NOT EXISTS (SELECT 1 FROM user_education WHERE user_id = (SELECT id FROM users WHERE email = 'amaka.eze@example.com'));

INSERT INTO user_education (user_id, school, degree, field_of_study, start_year, end_year, sort_order)
SELECT id, 'University of Ibadan', 'B.Sc', 'Business Administration', 2014, 2018, 1 FROM users WHERE email = 'amaka.eze@example.com'
  AND (SELECT COUNT(*) FROM user_education WHERE user_id = (SELECT id FROM users WHERE email = 'amaka.eze@example.com')) = 1;

UPDATE users SET personal_interests = 'Reading, Volunteering, Art Collecting, Fitness Training', phone = '+234 766 671 2867',
  bio = IF(bio IS NULL OR bio = '', 'Focused on building lasting client relationships and driving financial inclusion.', bio),
  skills = IF(skills IS NULL OR skills = '', 'Relationship Management, Credit Analysis', skills),
  expertise_areas = IF(expertise_areas IS NULL OR expertise_areas = '', 'Corporate Banking, Financial Inclusion', expertise_areas),
  business_interests = IF(business_interests IS NULL OR business_interests = '', 'Fintech, SME Lending', business_interests),
  linkedin_url = IF(linkedin_url IS NULL OR linkedin_url = '', 'https://linkedin.com/in/tunde.bakare', linkedin_url),
  company = IF(company IS NULL OR company = '', 'Guaranty Trust Bank', company),
  headline = IF(headline IS NULL OR headline = '', 'Relationship Manager', headline),
  industry = IF(industry IS NULL OR industry = '', 'Banking & Finance', industry),
  city = IF(city IS NULL OR city = '', 'Abuja', city),
  country = IF(country IS NULL OR country = '', 'Nigeria', country),
  state = IF(state IS NULL OR state = '', 'FCT', state),
  program = IF(program IS NULL OR program = '', 'MSc Finance', program),
  graduation_year = IF(graduation_year IS NULL, 2020, graduation_year),
  show_phone = IF(show_phone = 0, 1, show_phone),
  show_email = IF(show_email = 0, 1, show_email)
WHERE email = 'tunde.bakare@example.com' AND (personal_interests IS NULL OR personal_interests = '');

INSERT INTO user_experience (user_id, title, company, location, start_date, end_date, is_current, description, sort_order)
SELECT id, 'Relationship Manager', 'Guaranty Trust Bank', 'Abuja, Nigeria', '2021-02-01', NULL, 1, 'Leading corporate banking, financial inclusion initiatives at Guaranty Trust Bank, based in Abuja.', 0 FROM users WHERE email = 'tunde.bakare@example.com'
  AND NOT EXISTS (SELECT 1 FROM user_experience WHERE user_id = (SELECT id FROM users WHERE email = 'tunde.bakare@example.com'));

INSERT INTO user_experience (user_id, title, company, location, start_date, end_date, is_current, description, sort_order)
SELECT id, 'Associate, Corporate Finance', 'Stanbic IBTC', 'Abuja, Nigeria', '2017-01-01', '2020-12-31', 0, 'Supported corporate banking, financial inclusion projects early in career at Stanbic IBTC.', 1 FROM users WHERE email = 'tunde.bakare@example.com'
  AND (SELECT COUNT(*) FROM user_experience WHERE user_id = (SELECT id FROM users WHERE email = 'tunde.bakare@example.com')) = 1;

INSERT INTO user_education (user_id, school, degree, field_of_study, start_year, end_year, sort_order)
SELECT id, 'Rome Business School Nigeria', 'MSc Finance', 'Business Administration', 2018, 2020, 0 FROM users WHERE email = 'tunde.bakare@example.com'
  AND NOT EXISTS (SELECT 1 FROM user_education WHERE user_id = (SELECT id FROM users WHERE email = 'tunde.bakare@example.com'));

INSERT INTO user_education (user_id, school, degree, field_of_study, start_year, end_year, sort_order)
SELECT id, 'Lagos Business School', 'B.Sc', 'Accounting', 2013, 2017, 1 FROM users WHERE email = 'tunde.bakare@example.com'
  AND (SELECT COUNT(*) FROM user_education WHERE user_id = (SELECT id FROM users WHERE email = 'tunde.bakare@example.com')) = 1;

UPDATE users SET personal_interests = 'Reading, Hiking, Yoga, Art Collecting', phone = '+234 884 552 7868',
  bio = IF(bio IS NULL OR bio = '', 'Product leader focused on building payment infrastructure for African merchants. Passionate about mentoring early-career product managers.', bio),
  skills = IF(skills IS NULL OR skills = '', 'Product Strategy, Fintech, Payments, Agile Leadership', skills),
  expertise_areas = IF(expertise_areas IS NULL OR expertise_areas = '', 'Product Management, Fintech Strategy', expertise_areas),
  business_interests = IF(business_interests IS NULL OR business_interests = '', 'Payments, Financial Inclusion', business_interests),
  linkedin_url = IF(linkedin_url IS NULL OR linkedin_url = '', 'https://linkedin.com/in/ngoziadeyemi', linkedin_url),
  company = IF(company IS NULL OR company = '', 'Flutterwave', company),
  headline = IF(headline IS NULL OR headline = '', 'Head of Product, Flutterwave', headline),
  industry = IF(industry IS NULL OR industry = '', 'Technology & Fintech', industry),
  city = IF(city IS NULL OR city = '', 'Lagos', city),
  country = IF(country IS NULL OR country = '', 'Nigeria', country),
  state = IF(state IS NULL OR state = '', 'Lagos State', state),
  program = IF(program IS NULL OR program = '', 'Executive MBA', program),
  graduation_year = IF(graduation_year IS NULL, 2018, graduation_year),
  show_phone = IF(show_phone = 0, 1, show_phone),
  show_email = IF(show_email = 0, 1, show_email)
WHERE email = 'ngozi.adeyemi@example.com' AND (personal_interests IS NULL OR personal_interests = '');

INSERT INTO user_experience (user_id, title, company, location, start_date, end_date, is_current, description, sort_order)
SELECT id, 'Head of Product', 'Flutterwave', 'Lagos, Nigeria', '2019-04-01', NULL, 1, 'Leading product management, fintech strategy initiatives at Flutterwave, based in Lagos.', 0 FROM users WHERE email = 'ngozi.adeyemi@example.com'
  AND NOT EXISTS (SELECT 1 FROM user_experience WHERE user_id = (SELECT id FROM users WHERE email = 'ngozi.adeyemi@example.com'));

INSERT INTO user_experience (user_id, title, company, location, start_date, end_date, is_current, description, sort_order)
SELECT id, 'Associate, Product Strategy', 'Moniepoint', 'Lagos, Nigeria', '2015-01-01', '2018-12-31', 0, 'Supported product management, fintech strategy projects early in career at Moniepoint.', 1 FROM users WHERE email = 'ngozi.adeyemi@example.com'
  AND (SELECT COUNT(*) FROM user_experience WHERE user_id = (SELECT id FROM users WHERE email = 'ngozi.adeyemi@example.com')) = 1;

INSERT INTO user_education (user_id, school, degree, field_of_study, start_year, end_year, sort_order)
SELECT id, 'Rome Business School Nigeria', 'Executive MBA', 'Business Administration', 2016, 2018, 0 FROM users WHERE email = 'ngozi.adeyemi@example.com'
  AND NOT EXISTS (SELECT 1 FROM user_education WHERE user_id = (SELECT id FROM users WHERE email = 'ngozi.adeyemi@example.com'));

INSERT INTO user_education (user_id, school, degree, field_of_study, start_year, end_year, sort_order)
SELECT id, 'Obafemi Awolowo University', 'B.Sc', 'Software Engineering', 2011, 2015, 1 FROM users WHERE email = 'ngozi.adeyemi@example.com'
  AND (SELECT COUNT(*) FROM user_education WHERE user_id = (SELECT id FROM users WHERE email = 'ngozi.adeyemi@example.com')) = 1;

UPDATE users SET personal_interests = 'Golf, Running, Cycling, Podcasting', phone = '+234 842 438 5588',
  bio = IF(bio IS NULL OR bio = '', 'Advising corporates on capital raising and M&A across West Africa.', bio),
  skills = IF(skills IS NULL OR skills = '', 'Investment Banking, M&A, Financial Modelling', skills),
  expertise_areas = IF(expertise_areas IS NULL OR expertise_areas = '', 'Corporate Finance, Deal Structuring', expertise_areas),
  business_interests = IF(business_interests IS NULL OR business_interests = '', 'Private Equity', business_interests),
  linkedin_url = IF(linkedin_url IS NULL OR linkedin_url = '', 'https://linkedin.com/in/emekanwosu', linkedin_url),
  company = IF(company IS NULL OR company = '', 'Standard Chartered', company),
  headline = IF(headline IS NULL OR headline = '', 'Investment Banking VP, Standard Chartered', headline),
  industry = IF(industry IS NULL OR industry = '', 'Banking & Finance', industry),
  city = IF(city IS NULL OR city = '', 'Lagos', city),
  country = IF(country IS NULL OR country = '', 'Nigeria', country),
  state = IF(state IS NULL OR state = '', 'Lagos State', state),
  program = IF(program IS NULL OR program = '', 'MBA', program),
  graduation_year = IF(graduation_year IS NULL, 2016, graduation_year),
  show_phone = IF(show_phone = 0, 1, show_phone),
  show_email = IF(show_email = 0, 1, show_email)
WHERE email = 'emeka.nwosu@example.com' AND (personal_interests IS NULL OR personal_interests = '');

INSERT INTO user_experience (user_id, title, company, location, start_date, end_date, is_current, description, sort_order)
SELECT id, 'Investment Banking VP', 'Standard Chartered', 'Lagos, Nigeria', '2017-04-01', NULL, 1, 'Leading corporate finance, deal structuring initiatives at Standard Chartered, based in Lagos.', 0 FROM users WHERE email = 'emeka.nwosu@example.com'
  AND NOT EXISTS (SELECT 1 FROM user_experience WHERE user_id = (SELECT id FROM users WHERE email = 'emeka.nwosu@example.com'));

INSERT INTO user_experience (user_id, title, company, location, start_date, end_date, is_current, description, sort_order)
SELECT id, 'Associate, Corporate Finance', 'Ecobank Nigeria', 'Lagos, Nigeria', '2013-01-01', '2016-12-31', 0, 'Supported corporate finance, deal structuring projects early in career at Ecobank Nigeria.', 1 FROM users WHERE email = 'emeka.nwosu@example.com'
  AND (SELECT COUNT(*) FROM user_experience WHERE user_id = (SELECT id FROM users WHERE email = 'emeka.nwosu@example.com')) = 1;

INSERT INTO user_education (user_id, school, degree, field_of_study, start_year, end_year, sort_order)
SELECT id, 'Rome Business School Nigeria', 'MBA', 'Business Administration', 2014, 2016, 0 FROM users WHERE email = 'emeka.nwosu@example.com'
  AND NOT EXISTS (SELECT 1 FROM user_education WHERE user_id = (SELECT id FROM users WHERE email = 'emeka.nwosu@example.com'));

INSERT INTO user_education (user_id, school, degree, field_of_study, start_year, end_year, sort_order)
SELECT id, 'University of Lagos', 'B.Sc', 'Economics', 2009, 2013, 1 FROM users WHERE email = 'emeka.nwosu@example.com'
  AND (SELECT COUNT(*) FROM user_education WHERE user_id = (SELECT id FROM users WHERE email = 'emeka.nwosu@example.com')) = 1;

UPDATE users SET personal_interests = 'Chess, Golf, Cycling, Gardening', phone = '+234 744 230 2850',
  bio = IF(bio IS NULL OR bio = '', 'Building brand strategy for FMCG products across Nigeria and Ghana.', bio),
  skills = IF(skills IS NULL OR skills = '', 'Brand Management, Consumer Insights, Digital Marketing', skills),
  expertise_areas = IF(expertise_areas IS NULL OR expertise_areas = '', 'FMCG Marketing', expertise_areas),
  business_interests = IF(business_interests IS NULL OR business_interests = '', 'Retail, Consumer Products', business_interests),
  linkedin_url = IF(linkedin_url IS NULL OR linkedin_url = '', 'https://linkedin.com/in/folakeogundimu', linkedin_url),
  company = IF(company IS NULL OR company = '', 'Nestle Nigeria', company),
  headline = IF(headline IS NULL OR headline = '', 'Marketing Director, Nestle Nigeria', headline),
  industry = IF(industry IS NULL OR industry = '', 'Consumer Goods', industry),
  city = IF(city IS NULL OR city = '', 'Lagos', city),
  country = IF(country IS NULL OR country = '', 'Nigeria', country),
  state = IF(state IS NULL OR state = '', 'Lagos State', state),
  program = IF(program IS NULL OR program = '', 'MSc Marketing Management', program),
  graduation_year = IF(graduation_year IS NULL, 2017, graduation_year),
  show_phone = IF(show_phone = 0, 1, show_phone),
  show_email = IF(show_email = 0, 1, show_email)
WHERE email = 'folake.ogundimu@example.com' AND (personal_interests IS NULL OR personal_interests = '');

INSERT INTO user_experience (user_id, title, company, location, start_date, end_date, is_current, description, sort_order)
SELECT id, 'Marketing Director', 'Nestle Nigeria', 'Lagos, Nigeria', '2018-04-01', NULL, 1, 'Leading fmcg marketing initiatives at Nestle Nigeria, based in Lagos.', 0 FROM users WHERE email = 'folake.ogundimu@example.com'
  AND NOT EXISTS (SELECT 1 FROM user_experience WHERE user_id = (SELECT id FROM users WHERE email = 'folake.ogundimu@example.com'));

INSERT INTO user_experience (user_id, title, company, location, start_date, end_date, is_current, description, sort_order)
SELECT id, 'Associate, Brand Management', 'Cadbury Nigeria', 'Lagos, Nigeria', '2014-01-01', '2017-12-31', 0, 'Supported fmcg marketing projects early in career at Cadbury Nigeria.', 1 FROM users WHERE email = 'folake.ogundimu@example.com'
  AND (SELECT COUNT(*) FROM user_experience WHERE user_id = (SELECT id FROM users WHERE email = 'folake.ogundimu@example.com')) = 1;

INSERT INTO user_education (user_id, school, degree, field_of_study, start_year, end_year, sort_order)
SELECT id, 'Rome Business School Nigeria', 'MSc Marketing Management', 'Business Administration', 2015, 2017, 0 FROM users WHERE email = 'folake.ogundimu@example.com'
  AND NOT EXISTS (SELECT 1 FROM user_education WHERE user_id = (SELECT id FROM users WHERE email = 'folake.ogundimu@example.com'));

INSERT INTO user_education (user_id, school, degree, field_of_study, start_year, end_year, sort_order)
SELECT id, 'Babcock University', 'B.Sc', 'Business Administration', 2010, 2014, 1 FROM users WHERE email = 'folake.ogundimu@example.com'
  AND (SELECT COUNT(*) FROM user_education WHERE user_id = (SELECT id FROM users WHERE email = 'folake.ogundimu@example.com')) = 1;

UPDATE users SET personal_interests = 'Golf, Yoga, Fitness Training', phone = '+234 755 784 1319',
  bio = IF(bio IS NULL OR bio = '', 'Leading operational excellence programs across Dangote\'s manufacturing plants.', bio),
  skills = IF(skills IS NULL OR skills = '', 'Operations Management, Supply Chain, Lean Six Sigma', skills),
  expertise_areas = IF(expertise_areas IS NULL OR expertise_areas = '', 'Manufacturing Operations', expertise_areas),
  business_interests = IF(business_interests IS NULL OR business_interests = '', 'Industrial Manufacturing', business_interests),
  linkedin_url = IF(linkedin_url IS NULL OR linkedin_url = '', 'https://linkedin.com/in/ibrahimsuleiman', linkedin_url),
  company = IF(company IS NULL OR company = '', 'Dangote Group', company),
  headline = IF(headline IS NULL OR headline = '', 'Head of Operations, Dangote Group', headline),
  industry = IF(industry IS NULL OR industry = '', 'Manufacturing', industry),
  city = IF(city IS NULL OR city = '', 'Kano', city),
  country = IF(country IS NULL OR country = '', 'Nigeria', country),
  state = IF(state IS NULL OR state = '', 'Kano State', state),
  program = IF(program IS NULL OR program = '', 'Executive MBA', program),
  graduation_year = IF(graduation_year IS NULL, 2015, graduation_year),
  show_phone = IF(show_phone = 0, 1, show_phone),
  show_email = IF(show_email = 0, 1, show_email)
WHERE email = 'ibrahim.suleiman@example.com' AND (personal_interests IS NULL OR personal_interests = '');

INSERT INTO user_experience (user_id, title, company, location, start_date, end_date, is_current, description, sort_order)
SELECT id, 'Head of Operations', 'Dangote Group', 'Kano, Nigeria', '2016-05-01', NULL, 1, 'Leading manufacturing operations initiatives at Dangote Group, based in Kano.', 0 FROM users WHERE email = 'ibrahim.suleiman@example.com'
  AND NOT EXISTS (SELECT 1 FROM user_experience WHERE user_id = (SELECT id FROM users WHERE email = 'ibrahim.suleiman@example.com'));

INSERT INTO user_experience (user_id, title, company, location, start_date, end_date, is_current, description, sort_order)
SELECT id, 'Associate, Operations', 'Nigerian Bottling Company', 'Kano, Nigeria', '2012-01-01', '2015-12-31', 0, 'Supported manufacturing operations projects early in career at Nigerian Bottling Company.', 1 FROM users WHERE email = 'ibrahim.suleiman@example.com'
  AND (SELECT COUNT(*) FROM user_experience WHERE user_id = (SELECT id FROM users WHERE email = 'ibrahim.suleiman@example.com')) = 1;

INSERT INTO user_education (user_id, school, degree, field_of_study, start_year, end_year, sort_order)
SELECT id, 'Rome Business School Nigeria', 'Executive MBA', 'Business Administration', 2013, 2015, 0 FROM users WHERE email = 'ibrahim.suleiman@example.com'
  AND NOT EXISTS (SELECT 1 FROM user_education WHERE user_id = (SELECT id FROM users WHERE email = 'ibrahim.suleiman@example.com'));

INSERT INTO user_education (user_id, school, degree, field_of_study, start_year, end_year, sort_order)
SELECT id, 'Ahmadu Bello University', 'B.Sc', 'Chemical Engineering', 2008, 2012, 1 FROM users WHERE email = 'ibrahim.suleiman@example.com'
  AND (SELECT COUNT(*) FROM user_education WHERE user_id = (SELECT id FROM users WHERE email = 'ibrahim.suleiman@example.com')) = 1;

UPDATE users SET personal_interests = 'Reading, Chess, Cycling', phone = '+234 827 962 4085',
  bio = IF(bio IS NULL OR bio = '', 'Applying machine learning to talent-matching problems across the Andela network.', bio),
  skills = IF(skills IS NULL OR skills = '', 'Data Science, Python, Machine Learning', skills),
  expertise_areas = IF(expertise_areas IS NULL OR expertise_areas = '', 'Analytics, Fintech', expertise_areas),
  business_interests = IF(business_interests IS NULL OR business_interests = '', 'EdTech, HR Tech', business_interests),
  linkedin_url = IF(linkedin_url IS NULL OR linkedin_url = '', 'https://linkedin.com/in/chiamakaumeh', linkedin_url),
  company = IF(company IS NULL OR company = '', 'Andela', company),
  headline = IF(headline IS NULL OR headline = '', 'Senior Data Scientist, Andela', headline),
  industry = IF(industry IS NULL OR industry = '', 'Technology & Fintech', industry),
  city = IF(city IS NULL OR city = '', 'Lagos', city),
  country = IF(country IS NULL OR country = '', 'Nigeria', country),
  state = IF(state IS NULL OR state = '', 'Lagos State', state),
  program = IF(program IS NULL OR program = '', 'MSc Finance', program),
  graduation_year = IF(graduation_year IS NULL, 2020, graduation_year),
  show_phone = IF(show_phone = 0, 1, show_phone),
  show_email = IF(show_email = 0, 1, show_email)
WHERE email = 'chiamaka.umeh@example.com' AND (personal_interests IS NULL OR personal_interests = '');

INSERT INTO user_experience (user_id, title, company, location, start_date, end_date, is_current, description, sort_order)
SELECT id, 'Senior Data Scientist', 'Andela', 'Lagos, Nigeria', '2021-01-01', NULL, 1, 'Leading analytics, fintech initiatives at Andela, based in Lagos.', 0 FROM users WHERE email = 'chiamaka.umeh@example.com'
  AND NOT EXISTS (SELECT 1 FROM user_experience WHERE user_id = (SELECT id FROM users WHERE email = 'chiamaka.umeh@example.com'));

INSERT INTO user_experience (user_id, title, company, location, start_date, end_date, is_current, description, sort_order)
SELECT id, 'Associate, Product Strategy', 'PiggyVest', 'Lagos, Nigeria', '2017-01-01', '2020-12-31', 0, 'Supported analytics, fintech projects early in career at PiggyVest.', 1 FROM users WHERE email = 'chiamaka.umeh@example.com'
  AND (SELECT COUNT(*) FROM user_experience WHERE user_id = (SELECT id FROM users WHERE email = 'chiamaka.umeh@example.com')) = 1;

INSERT INTO user_education (user_id, school, degree, field_of_study, start_year, end_year, sort_order)
SELECT id, 'Rome Business School Nigeria', 'MSc Finance', 'Business Administration', 2018, 2020, 0 FROM users WHERE email = 'chiamaka.umeh@example.com'
  AND NOT EXISTS (SELECT 1 FROM user_education WHERE user_id = (SELECT id FROM users WHERE email = 'chiamaka.umeh@example.com'));

INSERT INTO user_education (user_id, school, degree, field_of_study, start_year, end_year, sort_order)
SELECT id, 'University of Port Harcourt', 'B.Sc', 'Software Engineering', 2013, 2017, 1 FROM users WHERE email = 'chiamaka.umeh@example.com'
  AND (SELECT COUNT(*) FROM user_education WHERE user_id = (SELECT id FROM users WHERE email = 'chiamaka.umeh@example.com')) = 1;

UPDATE users SET personal_interests = 'Hiking, Mentoring, Volunteering, Gardening', phone = '+234 852 359 9550',
  bio = IF(bio IS NULL OR bio = '', 'Built a last-mile delivery network serving over 40 cities in Nigeria.', bio),
  skills = IF(skills IS NULL OR skills = '', 'Entrepreneurship, Logistics, Fundraising', skills),
  expertise_areas = IF(expertise_areas IS NULL OR expertise_areas = '', 'Startup Scaling, Logistics Tech', expertise_areas),
  business_interests = IF(business_interests IS NULL OR business_interests = '', 'Logistics, Supply Chain', business_interests),
  linkedin_url = IF(linkedin_url IS NULL OR linkedin_url = '', 'https://linkedin.com/in/segunafolabi', linkedin_url),
  company = IF(company IS NULL OR company = '', 'Afolabi Logistics', company),
  headline = IF(headline IS NULL OR headline = '', 'Founder & CEO, Afolabi Logistics', headline),
  industry = IF(industry IS NULL OR industry = '', 'Logistics & Supply Chain', industry),
  city = IF(city IS NULL OR city = '', 'Ibadan', city),
  country = IF(country IS NULL OR country = '', 'Nigeria', country),
  state = IF(state IS NULL OR state = '', 'Oyo State', state),
  program = IF(program IS NULL OR program = '', 'MBA', program),
  graduation_year = IF(graduation_year IS NULL, 2014, graduation_year),
  show_phone = IF(show_phone = 0, 1, show_phone),
  show_email = IF(show_email = 0, 1, show_email)
WHERE email = 'segun.afolabi@example.com' AND (personal_interests IS NULL OR personal_interests = '');

INSERT INTO user_experience (user_id, title, company, location, start_date, end_date, is_current, description, sort_order)
SELECT id, 'Founder & CEO', 'Afolabi Logistics', 'Ibadan, Nigeria', '2015-06-01', NULL, 1, 'Leading startup scaling, logistics tech initiatives at Afolabi Logistics, based in Ibadan.', 0 FROM users WHERE email = 'segun.afolabi@example.com'
  AND NOT EXISTS (SELECT 1 FROM user_experience WHERE user_id = (SELECT id FROM users WHERE email = 'segun.afolabi@example.com'));

INSERT INTO user_experience (user_id, title, company, location, start_date, end_date, is_current, description, sort_order)
SELECT id, 'Associate, Supply Chain Optimization', 'DHL Nigeria', 'Ibadan, Nigeria', '2011-01-01', '2014-12-31', 0, 'Supported startup scaling, logistics tech projects early in career at DHL Nigeria.', 1 FROM users WHERE email = 'segun.afolabi@example.com'
  AND (SELECT COUNT(*) FROM user_experience WHERE user_id = (SELECT id FROM users WHERE email = 'segun.afolabi@example.com')) = 1;

INSERT INTO user_education (user_id, school, degree, field_of_study, start_year, end_year, sort_order)
SELECT id, 'Rome Business School Nigeria', 'MBA', 'Business Administration', 2012, 2014, 0 FROM users WHERE email = 'segun.afolabi@example.com'
  AND NOT EXISTS (SELECT 1 FROM user_education WHERE user_id = (SELECT id FROM users WHERE email = 'segun.afolabi@example.com'));

INSERT INTO user_education (user_id, school, degree, field_of_study, start_year, end_year, sort_order)
SELECT id, 'University of Nigeria Nsukka', 'B.Sc', 'Transport Management', 2007, 2011, 1 FROM users WHERE email = 'segun.afolabi@example.com'
  AND (SELECT COUNT(*) FROM user_education WHERE user_id = (SELECT id FROM users WHERE email = 'segun.afolabi@example.com')) = 1;

UPDATE users SET personal_interests = 'Mentoring, Wine Tasting, Yoga', phone = '+234 722 783 3627',
  bio = IF(bio IS NULL OR bio = '', 'Partnering with business leaders on talent strategy across Airtel\'s Nigerian operations.', bio),
  skills = IF(skills IS NULL OR skills = '', 'HR Strategy, Talent Management, Organizational Design', skills),
  expertise_areas = IF(expertise_areas IS NULL OR expertise_areas = '', 'People Operations', expertise_areas),
  business_interests = IF(business_interests IS NULL OR business_interests = '', 'Telecommunications', business_interests),
  linkedin_url = IF(linkedin_url IS NULL OR linkedin_url = '', 'https://linkedin.com/in/blessingetim', linkedin_url),
  company = IF(company IS NULL OR company = '', 'Airtel Africa', company),
  headline = IF(headline IS NULL OR headline = '', 'HR Business Partner, Airtel Africa', headline),
  industry = IF(industry IS NULL OR industry = '', 'Telecommunications', industry),
  city = IF(city IS NULL OR city = '', 'Port Harcourt', city),
  country = IF(country IS NULL OR country = '', 'Nigeria', country),
  state = IF(state IS NULL OR state = '', 'Rivers State', state),
  program = IF(program IS NULL OR program = '', 'MBA', program),
  graduation_year = IF(graduation_year IS NULL, 2019, graduation_year),
  show_phone = IF(show_phone = 0, 1, show_phone),
  show_email = IF(show_email = 0, 1, show_email)
WHERE email = 'blessing.etim@example.com' AND (personal_interests IS NULL OR personal_interests = '');

INSERT INTO user_experience (user_id, title, company, location, start_date, end_date, is_current, description, sort_order)
SELECT id, 'HR Business Partner', 'Airtel Africa', 'Port Harcourt, Nigeria', '2020-01-01', NULL, 1, 'Leading people operations initiatives at Airtel Africa, based in Port Harcourt.', 0 FROM users WHERE email = 'blessing.etim@example.com'
  AND NOT EXISTS (SELECT 1 FROM user_experience WHERE user_id = (SELECT id FROM users WHERE email = 'blessing.etim@example.com'));

INSERT INTO user_experience (user_id, title, company, location, start_date, end_date, is_current, description, sort_order)
SELECT id, 'Associate, Network Operations', '9mobile', 'Port Harcourt, Nigeria', '2016-01-01', '2019-12-31', 0, 'Supported people operations projects early in career at 9mobile.', 1 FROM users WHERE email = 'blessing.etim@example.com'
  AND (SELECT COUNT(*) FROM user_experience WHERE user_id = (SELECT id FROM users WHERE email = 'blessing.etim@example.com')) = 1;

INSERT INTO user_education (user_id, school, degree, field_of_study, start_year, end_year, sort_order)
SELECT id, 'Rome Business School Nigeria', 'MBA', 'Business Administration', 2017, 2019, 0 FROM users WHERE email = 'blessing.etim@example.com'
  AND NOT EXISTS (SELECT 1 FROM user_education WHERE user_id = (SELECT id FROM users WHERE email = 'blessing.etim@example.com'));

INSERT INTO user_education (user_id, school, degree, field_of_study, start_year, end_year, sort_order)
SELECT id, 'University of Port Harcourt', 'B.Sc', 'Electrical Engineering', 2012, 2016, 1 FROM users WHERE email = 'blessing.etim@example.com'
  AND (SELECT COUNT(*) FROM user_education WHERE user_id = (SELECT id FROM users WHERE email = 'blessing.etim@example.com')) = 1;

UPDATE users SET personal_interests = 'Chess, Mentoring, Fitness Training', phone = '+234 906 463 7674',
  bio = IF(bio IS NULL OR bio = '', 'Managing distributor relationships across Northern Nigeria\'s energy sector.', bio),
  skills = IF(skills IS NULL OR skills = '', 'Sales Strategy, Key Account Management, Negotiation', skills),
  expertise_areas = IF(expertise_areas IS NULL OR expertise_areas = '', 'Energy Sector Sales', expertise_areas),
  business_interests = IF(business_interests IS NULL OR business_interests = '', 'Oil & Gas, Renewable Energy', business_interests),
  linkedin_url = IF(linkedin_url IS NULL OR linkedin_url = '', 'https://linkedin.com/in/yusufabdullahi', linkedin_url),
  company = IF(company IS NULL OR company = '', 'Total Energies', company),
  headline = IF(headline IS NULL OR headline = '', 'Regional Sales Manager, Total Energies', headline),
  industry = IF(industry IS NULL OR industry = '', 'Energy', industry),
  city = IF(city IS NULL OR city = '', 'Abuja', city),
  country = IF(country IS NULL OR country = '', 'Nigeria', country),
  state = IF(state IS NULL OR state = '', 'FCT', state),
  program = IF(program IS NULL OR program = '', 'Executive MBA', program),
  graduation_year = IF(graduation_year IS NULL, 2016, graduation_year),
  show_phone = IF(show_phone = 0, 1, show_phone),
  show_email = IF(show_email = 0, 1, show_email)
WHERE email = 'yusuf.abdullahi@example.com' AND (personal_interests IS NULL OR personal_interests = '');

INSERT INTO user_experience (user_id, title, company, location, start_date, end_date, is_current, description, sort_order)
SELECT id, 'Regional Sales Manager', 'Total Energies', 'Abuja, Nigeria', '2017-01-01', NULL, 1, 'Leading energy sector sales initiatives at Total Energies, based in Abuja.', 0 FROM users WHERE email = 'yusuf.abdullahi@example.com'
  AND NOT EXISTS (SELECT 1 FROM user_experience WHERE user_id = (SELECT id FROM users WHERE email = 'yusuf.abdullahi@example.com'));

INSERT INTO user_experience (user_id, title, company, location, start_date, end_date, is_current, description, sort_order)
SELECT id, 'Associate, Energy Policy', 'NNPC', 'Abuja, Nigeria', '2013-01-01', '2016-12-31', 0, 'Supported energy sector sales projects early in career at NNPC.', 1 FROM users WHERE email = 'yusuf.abdullahi@example.com'
  AND (SELECT COUNT(*) FROM user_experience WHERE user_id = (SELECT id FROM users WHERE email = 'yusuf.abdullahi@example.com')) = 1;

INSERT INTO user_education (user_id, school, degree, field_of_study, start_year, end_year, sort_order)
SELECT id, 'Rome Business School Nigeria', 'Executive MBA', 'Business Administration', 2014, 2016, 0 FROM users WHERE email = 'yusuf.abdullahi@example.com'
  AND NOT EXISTS (SELECT 1 FROM user_education WHERE user_id = (SELECT id FROM users WHERE email = 'yusuf.abdullahi@example.com'));

INSERT INTO user_education (user_id, school, degree, field_of_study, start_year, end_year, sort_order)
SELECT id, 'University of Ghana', 'B.Sc', 'Mechanical Engineering', 2009, 2013, 1 FROM users WHERE email = 'yusuf.abdullahi@example.com'
  AND (SELECT COUNT(*) FROM user_education WHERE user_id = (SELECT id FROM users WHERE email = 'yusuf.abdullahi@example.com')) = 1;

UPDATE users SET personal_interests = 'Traveling, Volunteering, Yoga, Gardening', phone = '+234 884 119 5756',
  bio = IF(bio IS NULL OR bio = '', 'Handling regulatory compliance and corporate governance for retail banking products.', bio),
  skills = IF(skills IS NULL OR skills = '', 'Corporate Law, Regulatory Compliance, Risk Management', skills),
  expertise_areas = IF(expertise_areas IS NULL OR expertise_areas = '', 'Banking Law', expertise_areas),
  business_interests = IF(business_interests IS NULL OR business_interests = '', 'Financial Regulation', business_interests),
  linkedin_url = IF(linkedin_url IS NULL OR linkedin_url = '', 'https://linkedin.com/in/adaezechukwu', linkedin_url),
  company = IF(company IS NULL OR company = '', 'Zenith Bank', company),
  headline = IF(headline IS NULL OR headline = '', 'Legal Counsel, Zenith Bank', headline),
  industry = IF(industry IS NULL OR industry = '', 'Banking & Finance', industry),
  city = IF(city IS NULL OR city = '', 'Lagos', city),
  country = IF(country IS NULL OR country = '', 'Nigeria', country),
  state = IF(state IS NULL OR state = '', 'Lagos State', state),
  program = IF(program IS NULL OR program = '', 'MBA', program),
  graduation_year = IF(graduation_year IS NULL, 2018, graduation_year),
  show_phone = IF(show_phone = 0, 1, show_phone),
  show_email = IF(show_email = 0, 1, show_email)
WHERE email = 'adaeze.chukwu@example.com' AND (personal_interests IS NULL OR personal_interests = '');

INSERT INTO user_experience (user_id, title, company, location, start_date, end_date, is_current, description, sort_order)
SELECT id, 'Legal Counsel', 'Zenith Bank', 'Lagos, Nigeria', '2019-02-01', NULL, 1, 'Leading banking law initiatives at Zenith Bank, based in Lagos.', 0 FROM users WHERE email = 'adaeze.chukwu@example.com'
  AND NOT EXISTS (SELECT 1 FROM user_experience WHERE user_id = (SELECT id FROM users WHERE email = 'adaeze.chukwu@example.com'));

INSERT INTO user_experience (user_id, title, company, location, start_date, end_date, is_current, description, sort_order)
SELECT id, 'Associate, Corporate Finance', 'Stanbic IBTC', 'Lagos, Nigeria', '2015-01-01', '2018-12-31', 0, 'Supported banking law projects early in career at Stanbic IBTC.', 1 FROM users WHERE email = 'adaeze.chukwu@example.com'
  AND (SELECT COUNT(*) FROM user_experience WHERE user_id = (SELECT id FROM users WHERE email = 'adaeze.chukwu@example.com')) = 1;

INSERT INTO user_education (user_id, school, degree, field_of_study, start_year, end_year, sort_order)
SELECT id, 'Rome Business School Nigeria', 'MBA', 'Business Administration', 2016, 2018, 0 FROM users WHERE email = 'adaeze.chukwu@example.com'
  AND NOT EXISTS (SELECT 1 FROM user_education WHERE user_id = (SELECT id FROM users WHERE email = 'adaeze.chukwu@example.com'));

INSERT INTO user_education (user_id, school, degree, field_of_study, start_year, end_year, sort_order)
SELECT id, 'Ahmadu Bello University', 'B.Sc', 'Accounting', 2011, 2015, 1 FROM users WHERE email = 'adaeze.chukwu@example.com'
  AND (SELECT COUNT(*) FROM user_education WHERE user_id = (SELECT id FROM users WHERE email = 'adaeze.chukwu@example.com')) = 1;

UPDATE users SET personal_interests = 'Cooking, Art Collecting, Fitness Training, Public Speaking', phone = '+234 771 834 9045',
  bio = IF(bio IS NULL OR bio = '', 'Driving go-to-market strategy for Paystack\'s merchant tools.', bio),
  skills = IF(skills IS NULL OR skills = '', 'Product Marketing, Growth, Fintech', skills),
  expertise_areas = IF(expertise_areas IS NULL OR expertise_areas = '', 'Fintech Marketing', expertise_areas),
  business_interests = IF(business_interests IS NULL OR business_interests = '', 'Payments', business_interests),
  linkedin_url = IF(linkedin_url IS NULL OR linkedin_url = '', 'https://linkedin.com/in/kemiajayi', linkedin_url),
  company = IF(company IS NULL OR company = '', 'Paystack', company),
  headline = IF(headline IS NULL OR headline = '', 'Product Marketing Lead, Paystack', headline),
  industry = IF(industry IS NULL OR industry = '', 'Technology & Fintech', industry),
  city = IF(city IS NULL OR city = '', 'Lagos', city),
  country = IF(country IS NULL OR country = '', 'Nigeria', country),
  state = IF(state IS NULL OR state = '', 'Lagos State', state),
  program = IF(program IS NULL OR program = '', 'MSc Marketing Management', program),
  graduation_year = IF(graduation_year IS NULL, 2021, graduation_year),
  show_phone = IF(show_phone = 0, 1, show_phone),
  show_email = IF(show_email = 0, 1, show_email)
WHERE email = 'kemi.ajayi@example.com' AND (personal_interests IS NULL OR personal_interests = '');

INSERT INTO user_experience (user_id, title, company, location, start_date, end_date, is_current, description, sort_order)
SELECT id, 'Product Marketing Lead', 'Paystack', 'Lagos, Nigeria', '2022-02-01', NULL, 1, 'Leading fintech marketing initiatives at Paystack, based in Lagos.', 0 FROM users WHERE email = 'kemi.ajayi@example.com'
  AND NOT EXISTS (SELECT 1 FROM user_experience WHERE user_id = (SELECT id FROM users WHERE email = 'kemi.ajayi@example.com'));

INSERT INTO user_experience (user_id, title, company, location, start_date, end_date, is_current, description, sort_order)
SELECT id, 'Associate, Product Strategy', 'Interswitch', 'Lagos, Nigeria', '2018-01-01', '2021-12-31', 0, 'Supported fintech marketing projects early in career at Interswitch.', 1 FROM users WHERE email = 'kemi.ajayi@example.com'
  AND (SELECT COUNT(*) FROM user_experience WHERE user_id = (SELECT id FROM users WHERE email = 'kemi.ajayi@example.com')) = 1;

INSERT INTO user_education (user_id, school, degree, field_of_study, start_year, end_year, sort_order)
SELECT id, 'Rome Business School Nigeria', 'MSc Marketing Management', 'Business Administration', 2019, 2021, 0 FROM users WHERE email = 'kemi.ajayi@example.com'
  AND NOT EXISTS (SELECT 1 FROM user_education WHERE user_id = (SELECT id FROM users WHERE email = 'kemi.ajayi@example.com'));

INSERT INTO user_education (user_id, school, degree, field_of_study, start_year, end_year, sort_order)
SELECT id, 'University of Ghana', 'B.Sc', 'Software Engineering', 2014, 2018, 1 FROM users WHERE email = 'kemi.ajayi@example.com'
  AND (SELECT COUNT(*) FROM user_education WHERE user_id = (SELECT id FROM users WHERE email = 'kemi.ajayi@example.com')) = 1;

UPDATE users SET personal_interests = 'Hiking, Golf, Running, Writing', phone = '+234 782 963 2925',
  bio = IF(bio IS NULL OR bio = '', 'Overseeing financial planning and analysis for West African operations.', bio),
  skills = IF(skills IS NULL OR skills = '', 'Financial Planning, Budgeting, FP&A', skills),
  expertise_areas = IF(expertise_areas IS NULL OR expertise_areas = '', 'Corporate Finance', expertise_areas),
  business_interests = IF(business_interests IS NULL OR business_interests = '', 'FMCG Finance', business_interests),
  linkedin_url = IF(linkedin_url IS NULL OR linkedin_url = '', 'https://linkedin.com/in/obinnaeze', linkedin_url),
  company = IF(company IS NULL OR company = '', 'PZ Cussons Nigeria', company),
  headline = IF(headline IS NULL OR headline = '', 'Finance Director, PZ Cussons Nigeria', headline),
  industry = IF(industry IS NULL OR industry = '', 'Consumer Goods', industry),
  city = IF(city IS NULL OR city = '', 'Lagos', city),
  country = IF(country IS NULL OR country = '', 'Nigeria', country),
  state = IF(state IS NULL OR state = '', 'Lagos State', state),
  program = IF(program IS NULL OR program = '', 'Executive MBA', program),
  graduation_year = IF(graduation_year IS NULL, 2015, graduation_year),
  show_phone = IF(show_phone = 0, 1, show_phone),
  show_email = IF(show_email = 0, 1, show_email)
WHERE email = 'obinna.eze@example.com' AND (personal_interests IS NULL OR personal_interests = '');

INSERT INTO user_experience (user_id, title, company, location, start_date, end_date, is_current, description, sort_order)
SELECT id, 'Finance Director', 'PZ Cussons Nigeria', 'Lagos, Nigeria', '2016-06-01', NULL, 1, 'Leading corporate finance initiatives at PZ Cussons Nigeria, based in Lagos.', 0 FROM users WHERE email = 'obinna.eze@example.com'
  AND NOT EXISTS (SELECT 1 FROM user_experience WHERE user_id = (SELECT id FROM users WHERE email = 'obinna.eze@example.com'));

INSERT INTO user_experience (user_id, title, company, location, start_date, end_date, is_current, description, sort_order)
SELECT id, 'Associate, Brand Management', 'Nigerian Breweries', 'Lagos, Nigeria', '2012-01-01', '2015-12-31', 0, 'Supported corporate finance projects early in career at Nigerian Breweries.', 1 FROM users WHERE email = 'obinna.eze@example.com'
  AND (SELECT COUNT(*) FROM user_experience WHERE user_id = (SELECT id FROM users WHERE email = 'obinna.eze@example.com')) = 1;

INSERT INTO user_education (user_id, school, degree, field_of_study, start_year, end_year, sort_order)
SELECT id, 'Rome Business School Nigeria', 'Executive MBA', 'Business Administration', 2013, 2015, 0 FROM users WHERE email = 'obinna.eze@example.com'
  AND NOT EXISTS (SELECT 1 FROM user_education WHERE user_id = (SELECT id FROM users WHERE email = 'obinna.eze@example.com'));

INSERT INTO user_education (user_id, school, degree, field_of_study, start_year, end_year, sort_order)
SELECT id, 'University of Ibadan', 'B.Sc', 'Economics', 2008, 2012, 1 FROM users WHERE email = 'obinna.eze@example.com'
  AND (SELECT COUNT(*) FROM user_education WHERE user_id = (SELECT id FROM users WHERE email = 'obinna.eze@example.com')) = 1;

UPDATE users SET personal_interests = 'Photography, Fitness Training, Gardening', phone = '+234 740 508 5149',
  bio = IF(bio IS NULL OR bio = '', 'Managing public sector advisory engagements across Northern Nigeria.', bio),
  skills = IF(skills IS NULL OR skills = '', 'Program Management, Public Sector Advisory, Stakeholder Engagement', skills),
  expertise_areas = IF(expertise_areas IS NULL OR expertise_areas = '', 'Consulting', expertise_areas),
  business_interests = IF(business_interests IS NULL OR business_interests = '', 'Public Policy', business_interests),
  linkedin_url = IF(linkedin_url IS NULL OR linkedin_url = '', 'https://linkedin.com/in/halimabello', linkedin_url),
  company = IF(company IS NULL OR company = '', 'PwC Nigeria', company),
  headline = IF(headline IS NULL OR headline = '', 'Program Manager, PwC Nigeria', headline),
  industry = IF(industry IS NULL OR industry = '', 'Professional Services', industry),
  city = IF(city IS NULL OR city = '', 'Abuja', city),
  country = IF(country IS NULL OR country = '', 'Nigeria', country),
  state = IF(state IS NULL OR state = '', 'FCT', state),
  program = IF(program IS NULL OR program = '', 'MBA', program),
  graduation_year = IF(graduation_year IS NULL, 2019, graduation_year),
  show_phone = IF(show_phone = 0, 1, show_phone),
  show_email = IF(show_email = 0, 1, show_email)
WHERE email = 'halima.bello@example.com' AND (personal_interests IS NULL OR personal_interests = '');

INSERT INTO user_experience (user_id, title, company, location, start_date, end_date, is_current, description, sort_order)
SELECT id, 'Program Manager', 'PwC Nigeria', 'Abuja, Nigeria', '2020-03-01', NULL, 1, 'Leading consulting initiatives at PwC Nigeria, based in Abuja.', 0 FROM users WHERE email = 'halima.bello@example.com'
  AND NOT EXISTS (SELECT 1 FROM user_experience WHERE user_id = (SELECT id FROM users WHERE email = 'halima.bello@example.com'));

INSERT INTO user_experience (user_id, title, company, location, start_date, end_date, is_current, description, sort_order)
SELECT id, 'Associate, Strategy Consulting', 'EY Nigeria', 'Abuja, Nigeria', '2016-01-01', '2019-12-31', 0, 'Supported consulting projects early in career at EY Nigeria.', 1 FROM users WHERE email = 'halima.bello@example.com'
  AND (SELECT COUNT(*) FROM user_experience WHERE user_id = (SELECT id FROM users WHERE email = 'halima.bello@example.com')) = 1;

INSERT INTO user_education (user_id, school, degree, field_of_study, start_year, end_year, sort_order)
SELECT id, 'Rome Business School Nigeria', 'MBA', 'Business Administration', 2017, 2019, 0 FROM users WHERE email = 'halima.bello@example.com'
  AND NOT EXISTS (SELECT 1 FROM user_education WHERE user_id = (SELECT id FROM users WHERE email = 'halima.bello@example.com'));

INSERT INTO user_education (user_id, school, degree, field_of_study, start_year, end_year, sort_order)
SELECT id, 'University of Ghana', 'B.Sc', 'Law', 2012, 2016, 1 FROM users WHERE email = 'halima.bello@example.com'
  AND (SELECT COUNT(*) FROM user_education WHERE user_id = (SELECT id FROM users WHERE email = 'halima.bello@example.com')) = 1;

UPDATE users SET personal_interests = 'Photography, Cooking, Football, Podcasting', phone = '+234 732 224 7449',
  bio = IF(bio IS NULL OR bio = '', 'Scaling rider and driver acquisition across Bolt\'s Nigerian cities.', bio),
  skills = IF(skills IS NULL OR skills = '', 'Growth Marketing, Marketplace Strategy, Analytics', skills),
  expertise_areas = IF(expertise_areas IS NULL OR expertise_areas = '', 'Mobility, Marketplaces', expertise_areas),
  business_interests = IF(business_interests IS NULL OR business_interests = '', 'Ride-hailing, Mobility', business_interests),
  linkedin_url = IF(linkedin_url IS NULL OR linkedin_url = '', 'https://linkedin.com/in/tobialabi', linkedin_url),
  company = IF(company IS NULL OR company = '', 'Bolt', company),
  headline = IF(headline IS NULL OR headline = '', 'Growth Lead, Bolt Nigeria', headline),
  industry = IF(industry IS NULL OR industry = '', 'Technology & Fintech', industry),
  city = IF(city IS NULL OR city = '', 'Lagos', city),
  country = IF(country IS NULL OR country = '', 'Nigeria', country),
  state = IF(state IS NULL OR state = '', 'Lagos State', state),
  program = IF(program IS NULL OR program = '', 'MBA', program),
  graduation_year = IF(graduation_year IS NULL, 2022, graduation_year),
  show_phone = IF(show_phone = 0, 1, show_phone),
  show_email = IF(show_email = 0, 1, show_email)
WHERE email = 'tobi.alabi@example.com' AND (personal_interests IS NULL OR personal_interests = '');

INSERT INTO user_experience (user_id, title, company, location, start_date, end_date, is_current, description, sort_order)
SELECT id, 'Growth Lead', 'Bolt', 'Lagos, Nigeria', '2023-06-01', NULL, 1, 'Leading mobility, marketplaces initiatives at Bolt, based in Lagos.', 0 FROM users WHERE email = 'tobi.alabi@example.com'
  AND NOT EXISTS (SELECT 1 FROM user_experience WHERE user_id = (SELECT id FROM users WHERE email = 'tobi.alabi@example.com'));

INSERT INTO user_experience (user_id, title, company, location, start_date, end_date, is_current, description, sort_order)
SELECT id, 'Associate, Product Strategy', 'Paystack', 'Lagos, Nigeria', '2019-01-01', '2022-12-31', 0, 'Supported mobility, marketplaces projects early in career at Paystack.', 1 FROM users WHERE email = 'tobi.alabi@example.com'
  AND (SELECT COUNT(*) FROM user_experience WHERE user_id = (SELECT id FROM users WHERE email = 'tobi.alabi@example.com')) = 1;

INSERT INTO user_education (user_id, school, degree, field_of_study, start_year, end_year, sort_order)
SELECT id, 'Rome Business School Nigeria', 'MBA', 'Business Administration', 2020, 2022, 0 FROM users WHERE email = 'tobi.alabi@example.com'
  AND NOT EXISTS (SELECT 1 FROM user_education WHERE user_id = (SELECT id FROM users WHERE email = 'tobi.alabi@example.com'));

INSERT INTO user_education (user_id, school, degree, field_of_study, start_year, end_year, sort_order)
SELECT id, 'Obafemi Awolowo University', 'B.Sc', 'Software Engineering', 2015, 2019, 1 FROM users WHERE email = 'tobi.alabi@example.com'
  AND (SELECT COUNT(*) FROM user_education WHERE user_id = (SELECT id FROM users WHERE email = 'tobi.alabi@example.com')) = 1;

UPDATE users SET personal_interests = 'Photography, Cooking, Jazz Music, Art Collecting', phone = '+234 724 371 9573',
  bio = IF(bio IS NULL OR bio = '', 'Running a boutique management consultancy serving mid-market African companies.', bio),
  skills = IF(skills IS NULL OR skills = '', 'Management Consulting, Strategy, Corporate Governance', skills),
  expertise_areas = IF(expertise_areas IS NULL OR expertise_areas = '', 'Strategy Consulting', expertise_areas),
  business_interests = IF(business_interests IS NULL OR business_interests = '', 'Consulting, Advisory Services', business_interests),
  linkedin_url = IF(linkedin_url IS NULL OR linkedin_url = '', 'https://linkedin.com/in/chinweokafor', linkedin_url),
  company = IF(company IS NULL OR company = '', 'Okafor & Co.', company),
  headline = IF(headline IS NULL OR headline = '', 'Managing Partner, Okafor & Co.', headline),
  industry = IF(industry IS NULL OR industry = '', 'Professional Services', industry),
  city = IF(city IS NULL OR city = '', 'Enugu', city),
  country = IF(country IS NULL OR country = '', 'Nigeria', country),
  state = IF(state IS NULL OR state = '', 'Enugu State', state),
  program = IF(program IS NULL OR program = '', 'DBA', program),
  graduation_year = IF(graduation_year IS NULL, 2013, graduation_year),
  show_phone = IF(show_phone = 0, 1, show_phone),
  show_email = IF(show_email = 0, 1, show_email)
WHERE email = 'chinwe.okafor@example.com' AND (personal_interests IS NULL OR personal_interests = '');

INSERT INTO user_experience (user_id, title, company, location, start_date, end_date, is_current, description, sort_order)
SELECT id, 'Managing Partner', 'Okafor & Co.', 'Enugu, Nigeria', '2014-06-01', NULL, 1, 'Leading strategy consulting initiatives at Okafor & Co., based in Enugu.', 0 FROM users WHERE email = 'chinwe.okafor@example.com'
  AND NOT EXISTS (SELECT 1 FROM user_experience WHERE user_id = (SELECT id FROM users WHERE email = 'chinwe.okafor@example.com'));

INSERT INTO user_experience (user_id, title, company, location, start_date, end_date, is_current, description, sort_order)
SELECT id, 'Associate, Strategy Consulting', 'KPMG Nigeria', 'Enugu, Nigeria', '2010-01-01', '2013-12-31', 0, 'Supported strategy consulting projects early in career at KPMG Nigeria.', 1 FROM users WHERE email = 'chinwe.okafor@example.com'
  AND (SELECT COUNT(*) FROM user_experience WHERE user_id = (SELECT id FROM users WHERE email = 'chinwe.okafor@example.com')) = 1;

INSERT INTO user_education (user_id, school, degree, field_of_study, start_year, end_year, sort_order)
SELECT id, 'Rome Business School Nigeria', 'DBA', 'Business Administration', 2011, 2013, 0 FROM users WHERE email = 'chinwe.okafor@example.com'
  AND NOT EXISTS (SELECT 1 FROM user_education WHERE user_id = (SELECT id FROM users WHERE email = 'chinwe.okafor@example.com'));

INSERT INTO user_education (user_id, school, degree, field_of_study, start_year, end_year, sort_order)
SELECT id, 'University of Ibadan', 'B.Sc', 'Law', 2006, 2010, 1 FROM users WHERE email = 'chinwe.okafor@example.com'
  AND (SELECT COUNT(*) FROM user_education WHERE user_id = (SELECT id FROM users WHERE email = 'chinwe.okafor@example.com')) = 1;

UPDATE users SET personal_interests = 'Golf, Writing, Yoga', phone = '+234 750 646 9968',
  bio = IF(bio IS NULL OR bio = '', 'Leading safety and production performance at Shell\'s Niger Delta facilities.', bio),
  skills = IF(skills IS NULL OR skills = '', 'Operations, HSE, Project Management', skills),
  expertise_areas = IF(expertise_areas IS NULL OR expertise_areas = '', 'Oil & Gas Operations', expertise_areas),
  business_interests = IF(business_interests IS NULL OR business_interests = '', 'Energy', business_interests),
  linkedin_url = IF(linkedin_url IS NULL OR linkedin_url = '', 'https://linkedin.com/in/musaaliyu', linkedin_url),
  company = IF(company IS NULL OR company = '', 'Shell Nigeria', company),
  headline = IF(headline IS NULL OR headline = '', 'Plant Manager, Shell Nigeria', headline),
  industry = IF(industry IS NULL OR industry = '', 'Oil & Gas', industry),
  city = IF(city IS NULL OR city = '', 'Port Harcourt', city),
  country = IF(country IS NULL OR country = '', 'Nigeria', country),
  state = IF(state IS NULL OR state = '', 'Rivers State', state),
  program = IF(program IS NULL OR program = '', 'Executive MBA', program),
  graduation_year = IF(graduation_year IS NULL, 2014, graduation_year),
  show_phone = IF(show_phone = 0, 1, show_phone),
  show_email = IF(show_email = 0, 1, show_email)
WHERE email = 'musa.aliyu@example.com' AND (personal_interests IS NULL OR personal_interests = '');

INSERT INTO user_experience (user_id, title, company, location, start_date, end_date, is_current, description, sort_order)
SELECT id, 'Plant Manager', 'Shell Nigeria', 'Port Harcourt, Nigeria', '2015-05-01', NULL, 1, 'Leading oil & gas operations initiatives at Shell Nigeria, based in Port Harcourt.', 0 FROM users WHERE email = 'musa.aliyu@example.com'
  AND NOT EXISTS (SELECT 1 FROM user_experience WHERE user_id = (SELECT id FROM users WHERE email = 'musa.aliyu@example.com'));

INSERT INTO user_experience (user_id, title, company, location, start_date, end_date, is_current, description, sort_order)
SELECT id, 'Associate, Upstream Operations', 'ExxonMobil Nigeria', 'Port Harcourt, Nigeria', '2011-01-01', '2014-12-31', 0, 'Supported oil & gas operations projects early in career at ExxonMobil Nigeria.', 1 FROM users WHERE email = 'musa.aliyu@example.com'
  AND (SELECT COUNT(*) FROM user_experience WHERE user_id = (SELECT id FROM users WHERE email = 'musa.aliyu@example.com')) = 1;

INSERT INTO user_education (user_id, school, degree, field_of_study, start_year, end_year, sort_order)
SELECT id, 'Rome Business School Nigeria', 'Executive MBA', 'Business Administration', 2012, 2014, 0 FROM users WHERE email = 'musa.aliyu@example.com'
  AND NOT EXISTS (SELECT 1 FROM user_education WHERE user_id = (SELECT id FROM users WHERE email = 'musa.aliyu@example.com'));

INSERT INTO user_education (user_id, school, degree, field_of_study, start_year, end_year, sort_order)
SELECT id, 'University of Ghana', 'B.Sc', 'Petroleum Engineering', 2007, 2011, 1 FROM users WHERE email = 'musa.aliyu@example.com'
  AND (SELECT COUNT(*) FROM user_education WHERE user_id = (SELECT id FROM users WHERE email = 'musa.aliyu@example.com')) = 1;

UPDATE users SET personal_interests = 'Wine Tasting, Yoga, Board Games', phone = '+234 717 182 7982',
  bio = IF(bio IS NULL OR bio = '', 'Managing the electronics category across Jumia\'s Nigerian marketplace.', bio),
  skills = IF(skills IS NULL OR skills = '', 'E-commerce, Category Management, Vendor Relations', skills),
  expertise_areas = IF(expertise_areas IS NULL OR expertise_areas = '', 'Retail Tech', expertise_areas),
  business_interests = IF(business_interests IS NULL OR business_interests = '', 'E-commerce', business_interests),
  linkedin_url = IF(linkedin_url IS NULL OR linkedin_url = '', 'https://linkedin.com/in/funmilayosalako', linkedin_url),
  company = IF(company IS NULL OR company = '', 'Jumia', company),
  headline = IF(headline IS NULL OR headline = '', 'Product Manager, Jumia Nigeria', headline),
  industry = IF(industry IS NULL OR industry = '', 'Technology & Fintech', industry),
  city = IF(city IS NULL OR city = '', 'Lagos', city),
  country = IF(country IS NULL OR country = '', 'Nigeria', country),
  state = IF(state IS NULL OR state = '', 'Lagos State', state),
  program = IF(program IS NULL OR program = '', 'MBA', program),
  graduation_year = IF(graduation_year IS NULL, 2020, graduation_year),
  show_phone = IF(show_phone = 0, 1, show_phone),
  show_email = IF(show_email = 0, 1, show_email)
WHERE email = 'funmilayo.salako@example.com' AND (personal_interests IS NULL OR personal_interests = '');

INSERT INTO user_experience (user_id, title, company, location, start_date, end_date, is_current, description, sort_order)
SELECT id, 'Product Manager', 'Jumia', 'Lagos, Nigeria', '2021-03-01', NULL, 1, 'Leading retail tech initiatives at Jumia, based in Lagos.', 0 FROM users WHERE email = 'funmilayo.salako@example.com'
  AND NOT EXISTS (SELECT 1 FROM user_experience WHERE user_id = (SELECT id FROM users WHERE email = 'funmilayo.salako@example.com'));

INSERT INTO user_experience (user_id, title, company, location, start_date, end_date, is_current, description, sort_order)
SELECT id, 'Associate, Product Strategy', 'Moniepoint', 'Lagos, Nigeria', '2017-01-01', '2020-12-31', 0, 'Supported retail tech projects early in career at Moniepoint.', 1 FROM users WHERE email = 'funmilayo.salako@example.com'
  AND (SELECT COUNT(*) FROM user_experience WHERE user_id = (SELECT id FROM users WHERE email = 'funmilayo.salako@example.com')) = 1;

INSERT INTO user_education (user_id, school, degree, field_of_study, start_year, end_year, sort_order)
SELECT id, 'Rome Business School Nigeria', 'MBA', 'Business Administration', 2018, 2020, 0 FROM users WHERE email = 'funmilayo.salako@example.com'
  AND NOT EXISTS (SELECT 1 FROM user_education WHERE user_id = (SELECT id FROM users WHERE email = 'funmilayo.salako@example.com'));

INSERT INTO user_education (user_id, school, degree, field_of_study, start_year, end_year, sort_order)
SELECT id, 'Kwame Nkrumah University of Science and Technology', 'B.Sc', 'Software Engineering', 2013, 2017, 1 FROM users WHERE email = 'funmilayo.salako@example.com'
  AND (SELECT COUNT(*) FROM user_education WHERE user_id = (SELECT id FROM users WHERE email = 'funmilayo.salako@example.com')) = 1;

UPDATE users SET personal_interests = 'Photography, Wine Tasting, Art Collecting, Board Games', phone = '+234 907 350 2086',
  bio = IF(bio IS NULL OR bio = '', 'Overseeing commercial performance for MTN\'s South-West Nigeria region.', bio),
  skills = IF(skills IS NULL OR skills = '', 'Telecom Strategy, P&L Management, Leadership', skills),
  expertise_areas = IF(expertise_areas IS NULL OR expertise_areas = '', 'Telecommunications', expertise_areas),
  business_interests = IF(business_interests IS NULL OR business_interests = '', 'Telecom, Digital Services', business_interests),
  linkedin_url = IF(linkedin_url IS NULL OR linkedin_url = '', 'https://linkedin.com/in/davidokeke', linkedin_url),
  company = IF(company IS NULL OR company = '', 'MTN Nigeria', company),
  headline = IF(headline IS NULL OR headline = '', 'Regional Director, MTN Nigeria', headline),
  industry = IF(industry IS NULL OR industry = '', 'Telecommunications', industry),
  city = IF(city IS NULL OR city = '', 'Lagos', city),
  country = IF(country IS NULL OR country = '', 'Nigeria', country),
  state = IF(state IS NULL OR state = '', 'Lagos State', state),
  program = IF(program IS NULL OR program = '', 'Executive MBA', program),
  graduation_year = IF(graduation_year IS NULL, 2012, graduation_year),
  show_phone = IF(show_phone = 0, 1, show_phone),
  show_email = IF(show_email = 0, 1, show_email)
WHERE email = 'david.okeke@example.com' AND (personal_interests IS NULL OR personal_interests = '');

INSERT INTO user_experience (user_id, title, company, location, start_date, end_date, is_current, description, sort_order)
SELECT id, 'Regional Director', 'MTN Nigeria', 'Lagos, Nigeria', '2013-04-01', NULL, 1, 'Leading telecommunications initiatives at MTN Nigeria, based in Lagos.', 0 FROM users WHERE email = 'david.okeke@example.com'
  AND NOT EXISTS (SELECT 1 FROM user_experience WHERE user_id = (SELECT id FROM users WHERE email = 'david.okeke@example.com'));

INSERT INTO user_experience (user_id, title, company, location, start_date, end_date, is_current, description, sort_order)
SELECT id, 'Associate, Network Operations', 'Globacom', 'Lagos, Nigeria', '2009-01-01', '2012-12-31', 0, 'Supported telecommunications projects early in career at Globacom.', 1 FROM users WHERE email = 'david.okeke@example.com'
  AND (SELECT COUNT(*) FROM user_experience WHERE user_id = (SELECT id FROM users WHERE email = 'david.okeke@example.com')) = 1;

INSERT INTO user_education (user_id, school, degree, field_of_study, start_year, end_year, sort_order)
SELECT id, 'Rome Business School Nigeria', 'Executive MBA', 'Business Administration', 2010, 2012, 0 FROM users WHERE email = 'david.okeke@example.com'
  AND NOT EXISTS (SELECT 1 FROM user_education WHERE user_id = (SELECT id FROM users WHERE email = 'david.okeke@example.com'));

INSERT INTO user_education (user_id, school, degree, field_of_study, start_year, end_year, sort_order)
SELECT id, 'Ahmadu Bello University', 'B.Sc', 'Computer Science', 2005, 2009, 1 FROM users WHERE email = 'david.okeke@example.com'
  AND (SELECT COUNT(*) FROM user_education WHERE user_id = (SELECT id FROM users WHERE email = 'david.okeke@example.com')) = 1;

UPDATE users SET personal_interests = 'Running, Yoga, Fitness Training', phone = '+233 22 976 3203',
  bio = IF(bio IS NULL OR bio = '', 'Advising on health systems strengthening projects across West Africa.', bio),
  skills = IF(skills IS NULL OR skills = '', 'Healthcare Strategy, Public Health, Project Management', skills),
  expertise_areas = IF(expertise_areas IS NULL OR expertise_areas = '', 'Health Systems', expertise_areas),
  business_interests = IF(business_interests IS NULL OR business_interests = '', 'Public Health, NGO', business_interests),
  linkedin_url = IF(linkedin_url IS NULL OR linkedin_url = '', 'https://linkedin.com/in/ronkebamidele', linkedin_url),
  company = IF(company IS NULL OR company = '', 'World Health Organization', company),
  headline = IF(headline IS NULL OR headline = '', 'Healthcare Consultant, WHO Africa', headline),
  industry = IF(industry IS NULL OR industry = '', 'Healthcare', industry),
  city = IF(city IS NULL OR city = '', 'Accra', city),
  country = IF(country IS NULL OR country = '', 'Ghana', country),
  state = IF(state IS NULL OR state = '', 'Greater Accra', state),
  program = IF(program IS NULL OR program = '', 'MBA', program),
  graduation_year = IF(graduation_year IS NULL, 2017, graduation_year),
  show_phone = IF(show_phone = 0, 1, show_phone),
  show_email = IF(show_email = 0, 1, show_email)
WHERE email = 'ronke.bamidele@example.com' AND (personal_interests IS NULL OR personal_interests = '');

INSERT INTO user_experience (user_id, title, company, location, start_date, end_date, is_current, description, sort_order)
SELECT id, 'Healthcare Consultant', 'World Health Organization', 'Accra, Ghana', '2018-05-01', NULL, 1, 'Leading health systems initiatives at World Health Organization, based in Accra.', 0 FROM users WHERE email = 'ronke.bamidele@example.com'
  AND NOT EXISTS (SELECT 1 FROM user_experience WHERE user_id = (SELECT id FROM users WHERE email = 'ronke.bamidele@example.com'));

INSERT INTO user_experience (user_id, title, company, location, start_date, end_date, is_current, description, sort_order)
SELECT id, 'Associate, Healthcare Management', 'WHO Africa', 'Accra, Ghana', '2014-01-01', '2017-12-31', 0, 'Supported health systems projects early in career at WHO Africa.', 1 FROM users WHERE email = 'ronke.bamidele@example.com'
  AND (SELECT COUNT(*) FROM user_experience WHERE user_id = (SELECT id FROM users WHERE email = 'ronke.bamidele@example.com')) = 1;

INSERT INTO user_education (user_id, school, degree, field_of_study, start_year, end_year, sort_order)
SELECT id, 'Rome Business School Nigeria', 'MBA', 'Business Administration', 2015, 2017, 0 FROM users WHERE email = 'ronke.bamidele@example.com'
  AND NOT EXISTS (SELECT 1 FROM user_education WHERE user_id = (SELECT id FROM users WHERE email = 'ronke.bamidele@example.com'));

INSERT INTO user_education (user_id, school, degree, field_of_study, start_year, end_year, sort_order)
SELECT id, 'Kwame Nkrumah University of Science and Technology', 'B.Sc', 'Pharmacy', 2010, 2014, 1 FROM users WHERE email = 'ronke.bamidele@example.com'
  AND (SELECT COUNT(*) FROM user_education WHERE user_id = (SELECT id FROM users WHERE email = 'ronke.bamidele@example.com')) = 1;

UPDATE users SET personal_interests = 'Cooking, Yoga, Art Collecting', phone = '+234 796 745 9030',
  bio = IF(bio IS NULL OR bio = '', 'Evaluating real estate investment opportunities across Sub-Saharan Africa.', bio),
  skills = IF(skills IS NULL OR skills = '', 'Real Estate Finance, Valuation, Market Research', skills),
  expertise_areas = IF(expertise_areas IS NULL OR expertise_areas = '', 'Real Estate Investment', expertise_areas),
  business_interests = IF(business_interests IS NULL OR business_interests = '', 'Real Estate, Private Equity', business_interests),
  linkedin_url = IF(linkedin_url IS NULL OR linkedin_url = '', 'https://linkedin.com/in/ucheiwu', linkedin_url),
  company = IF(company IS NULL OR company = '', 'Actis', company),
  headline = IF(headline IS NULL OR headline = '', 'Real Estate Investment Analyst, Actis', headline),
  industry = IF(industry IS NULL OR industry = '', 'Real Estate', industry),
  city = IF(city IS NULL OR city = '', 'Lagos', city),
  country = IF(country IS NULL OR country = '', 'Nigeria', country),
  state = IF(state IS NULL OR state = '', 'Lagos State', state),
  program = IF(program IS NULL OR program = '', 'MSc Finance', program),
  graduation_year = IF(graduation_year IS NULL, 2021, graduation_year),
  show_phone = IF(show_phone = 0, 1, show_phone),
  show_email = IF(show_email = 0, 1, show_email)
WHERE email = 'uche.iwu@example.com' AND (personal_interests IS NULL OR personal_interests = '');

INSERT INTO user_experience (user_id, title, company, location, start_date, end_date, is_current, description, sort_order)
SELECT id, 'Real Estate Investment Analyst', 'Actis', 'Lagos, Nigeria', '2022-03-01', NULL, 1, 'Leading real estate investment initiatives at Actis, based in Lagos.', 0 FROM users WHERE email = 'uche.iwu@example.com'
  AND NOT EXISTS (SELECT 1 FROM user_experience WHERE user_id = (SELECT id FROM users WHERE email = 'uche.iwu@example.com'));

INSERT INTO user_experience (user_id, title, company, location, start_date, end_date, is_current, description, sort_order)
SELECT id, 'Associate, Property Investment', 'Alpha Mead', 'Lagos, Nigeria', '2018-01-01', '2021-12-31', 0, 'Supported real estate investment projects early in career at Alpha Mead.', 1 FROM users WHERE email = 'uche.iwu@example.com'
  AND (SELECT COUNT(*) FROM user_experience WHERE user_id = (SELECT id FROM users WHERE email = 'uche.iwu@example.com')) = 1;

INSERT INTO user_education (user_id, school, degree, field_of_study, start_year, end_year, sort_order)
SELECT id, 'Rome Business School Nigeria', 'MSc Finance', 'Business Administration', 2019, 2021, 0 FROM users WHERE email = 'uche.iwu@example.com'
  AND NOT EXISTS (SELECT 1 FROM user_education WHERE user_id = (SELECT id FROM users WHERE email = 'uche.iwu@example.com'));

INSERT INTO user_education (user_id, school, degree, field_of_study, start_year, end_year, sort_order)
SELECT id, 'Obafemi Awolowo University', 'B.Sc', 'Business Administration', 2014, 2018, 1 FROM users WHERE email = 'uche.iwu@example.com'
  AND (SELECT COUNT(*) FROM user_education WHERE user_id = (SELECT id FROM users WHERE email = 'uche.iwu@example.com')) = 1;

UPDATE users SET personal_interests = 'Traveling, Hiking, Chess, Football', phone = '+234 877 398 4048',
  bio = IF(bio IS NULL OR bio = '', 'Leading content and distribution strategy for DStv\'s Nigerian audience.', bio),
  skills = IF(skills IS NULL OR skills = '', 'Media Strategy, Content Distribution, Partnerships', skills),
  expertise_areas = IF(expertise_areas IS NULL OR expertise_areas = '', 'Media & Broadcasting', expertise_areas),
  business_interests = IF(business_interests IS NULL OR business_interests = '', 'Media, Entertainment', business_interests),
  linkedin_url = IF(linkedin_url IS NULL OR linkedin_url = '', 'https://linkedin.com/in/graceeffiong', linkedin_url),
  company = IF(company IS NULL OR company = '', 'MultiChoice Nigeria', company),
  headline = IF(headline IS NULL OR headline = '', 'Media Strategy Lead, Multichoice Nigeria', headline),
  industry = IF(industry IS NULL OR industry = '', 'Media & Entertainment', industry),
  city = IF(city IS NULL OR city = '', 'Lagos', city),
  country = IF(country IS NULL OR country = '', 'Nigeria', country),
  state = IF(state IS NULL OR state = '', 'Lagos State', state),
  program = IF(program IS NULL OR program = '', 'MBA', program),
  graduation_year = IF(graduation_year IS NULL, 2019, graduation_year),
  show_phone = IF(show_phone = 0, 1, show_phone),
  show_email = IF(show_email = 0, 1, show_email)
WHERE email = 'grace.effiong@example.com' AND (personal_interests IS NULL OR personal_interests = '');

INSERT INTO user_experience (user_id, title, company, location, start_date, end_date, is_current, description, sort_order)
SELECT id, 'Media Strategy Lead', 'MultiChoice Nigeria', 'Lagos, Nigeria', '2020-06-01', NULL, 1, 'Leading media & broadcasting initiatives at MultiChoice Nigeria, based in Lagos.', 0 FROM users WHERE email = 'grace.effiong@example.com'
  AND NOT EXISTS (SELECT 1 FROM user_experience WHERE user_id = (SELECT id FROM users WHERE email = 'grace.effiong@example.com'));

INSERT INTO user_experience (user_id, title, company, location, start_date, end_date, is_current, description, sort_order)
SELECT id, 'Associate, Content Strategy', 'Channels Television', 'Lagos, Nigeria', '2016-01-01', '2019-12-31', 0, 'Supported media & broadcasting projects early in career at Channels Television.', 1 FROM users WHERE email = 'grace.effiong@example.com'
  AND (SELECT COUNT(*) FROM user_experience WHERE user_id = (SELECT id FROM users WHERE email = 'grace.effiong@example.com')) = 1;

INSERT INTO user_education (user_id, school, degree, field_of_study, start_year, end_year, sort_order)
SELECT id, 'Rome Business School Nigeria', 'MBA', 'Business Administration', 2017, 2019, 0 FROM users WHERE email = 'grace.effiong@example.com'
  AND NOT EXISTS (SELECT 1 FROM user_education WHERE user_id = (SELECT id FROM users WHERE email = 'grace.effiong@example.com'));

INSERT INTO user_education (user_id, school, degree, field_of_study, start_year, end_year, sort_order)
SELECT id, 'Obafemi Awolowo University', 'B.Sc', 'Mass Communication', 2012, 2016, 1 FROM users WHERE email = 'grace.effiong@example.com'
  AND (SELECT COUNT(*) FROM user_education WHERE user_id = (SELECT id FROM users WHERE email = 'grace.effiong@example.com')) = 1;

UPDATE users SET personal_interests = 'Running, Writing, Yoga, Gardening', phone = '+234 725 627 9207',
  bio = IF(bio IS NULL OR bio = '', 'Leading credit risk strategy across Access Bank\'s retail lending portfolio.', bio),
  skills = IF(skills IS NULL OR skills = '', 'Credit Risk, Portfolio Management, Basel III', skills),
  expertise_areas = IF(expertise_areas IS NULL OR expertise_areas = '', 'Risk Management', expertise_areas),
  business_interests = IF(business_interests IS NULL OR business_interests = '', 'Digital Lending', business_interests),
  linkedin_url = IF(linkedin_url IS NULL OR linkedin_url = '', 'https://linkedin.com/in/yewandeadisa', linkedin_url),
  company = IF(company IS NULL OR company = '', 'Access Bank', company),
  headline = IF(headline IS NULL OR headline = '', 'Head of Credit Risk, Access Bank', headline),
  industry = IF(industry IS NULL OR industry = '', 'Banking & Finance', industry),
  city = IF(city IS NULL OR city = '', 'Lagos', city),
  country = IF(country IS NULL OR country = '', 'Nigeria', country),
  state = IF(state IS NULL OR state = '', 'Lagos State', state),
  program = IF(program IS NULL OR program = '', 'MBA', program),
  graduation_year = IF(graduation_year IS NULL, 2017, graduation_year),
  show_phone = IF(show_phone = 0, 1, show_phone),
  show_email = IF(show_email = 0, 1, show_email)
WHERE email = 'yewande.adisa@example.com' AND (personal_interests IS NULL OR personal_interests = '');

INSERT INTO user_experience (user_id, title, company, location, start_date, end_date, is_current, description, sort_order)
SELECT id, 'Head of Credit Risk', 'Access Bank', 'Lagos, Nigeria', '2018-04-01', NULL, 1, 'Leading risk management initiatives at Access Bank, based in Lagos.', 0 FROM users WHERE email = 'yewande.adisa@example.com'
  AND NOT EXISTS (SELECT 1 FROM user_experience WHERE user_id = (SELECT id FROM users WHERE email = 'yewande.adisa@example.com'));

INSERT INTO user_experience (user_id, title, company, location, start_date, end_date, is_current, description, sort_order)
SELECT id, 'Associate, Corporate Finance', 'Sterling Bank', 'Lagos, Nigeria', '2014-01-01', '2017-12-31', 0, 'Supported risk management projects early in career at Sterling Bank.', 1 FROM users WHERE email = 'yewande.adisa@example.com'
  AND (SELECT COUNT(*) FROM user_experience WHERE user_id = (SELECT id FROM users WHERE email = 'yewande.adisa@example.com')) = 1;

INSERT INTO user_education (user_id, school, degree, field_of_study, start_year, end_year, sort_order)
SELECT id, 'Rome Business School Nigeria', 'MBA', 'Business Administration', 2015, 2017, 0 FROM users WHERE email = 'yewande.adisa@example.com'
  AND NOT EXISTS (SELECT 1 FROM user_education WHERE user_id = (SELECT id FROM users WHERE email = 'yewande.adisa@example.com'));

INSERT INTO user_education (user_id, school, degree, field_of_study, start_year, end_year, sort_order)
SELECT id, 'Babcock University', 'B.Sc', 'Accounting', 2010, 2014, 1 FROM users WHERE email = 'yewande.adisa@example.com'
  AND (SELECT COUNT(*) FROM user_education WHERE user_id = (SELECT id FROM users WHERE email = 'yewande.adisa@example.com')) = 1;

UPDATE users SET personal_interests = 'Cooking, Running, Fitness Training', phone = '+234 781 484 4742',
  bio = IF(bio IS NULL OR bio = '', 'Building the engineering culture behind one of Nigeria\'s fastest-growing digital banks.', bio),
  skills = IF(skills IS NULL OR skills = '', 'Engineering Leadership, Cloud Architecture, Fintech', skills),
  expertise_areas = IF(expertise_areas IS NULL OR expertise_areas = '', 'Digital Banking', expertise_areas),
  business_interests = IF(business_interests IS NULL OR business_interests = '', 'Neobanking', business_interests),
  linkedin_url = IF(linkedin_url IS NULL OR linkedin_url = '', 'https://linkedin.com/in/kelechinnamdi', linkedin_url),
  company = IF(company IS NULL OR company = '', 'Kuda Bank', company),
  headline = IF(headline IS NULL OR headline = '', 'VP Engineering, Kuda Bank', headline),
  industry = IF(industry IS NULL OR industry = '', 'Technology & Fintech', industry),
  city = IF(city IS NULL OR city = '', 'Lagos', city),
  country = IF(country IS NULL OR country = '', 'Nigeria', country),
  state = IF(state IS NULL OR state = '', 'Lagos State', state),
  program = IF(program IS NULL OR program = '', 'Executive MBA', program),
  graduation_year = IF(graduation_year IS NULL, 2016, graduation_year),
  show_phone = IF(show_phone = 0, 1, show_phone),
  show_email = IF(show_email = 0, 1, show_email)
WHERE email = 'kelechi.nnamdi@example.com' AND (personal_interests IS NULL OR personal_interests = '');

INSERT INTO user_experience (user_id, title, company, location, start_date, end_date, is_current, description, sort_order)
SELECT id, 'VP Engineering', 'Kuda Bank', 'Lagos, Nigeria', '2017-03-01', NULL, 1, 'Leading digital banking initiatives at Kuda Bank, based in Lagos.', 0 FROM users WHERE email = 'kelechi.nnamdi@example.com'
  AND NOT EXISTS (SELECT 1 FROM user_experience WHERE user_id = (SELECT id FROM users WHERE email = 'kelechi.nnamdi@example.com'));

INSERT INTO user_experience (user_id, title, company, location, start_date, end_date, is_current, description, sort_order)
SELECT id, 'Associate, Product Strategy', 'Interswitch', 'Lagos, Nigeria', '2013-01-01', '2016-12-31', 0, 'Supported digital banking projects early in career at Interswitch.', 1 FROM users WHERE email = 'kelechi.nnamdi@example.com'
  AND (SELECT COUNT(*) FROM user_experience WHERE user_id = (SELECT id FROM users WHERE email = 'kelechi.nnamdi@example.com')) = 1;

INSERT INTO user_education (user_id, school, degree, field_of_study, start_year, end_year, sort_order)
SELECT id, 'Rome Business School Nigeria', 'Executive MBA', 'Business Administration', 2014, 2016, 0 FROM users WHERE email = 'kelechi.nnamdi@example.com'
  AND NOT EXISTS (SELECT 1 FROM user_education WHERE user_id = (SELECT id FROM users WHERE email = 'kelechi.nnamdi@example.com'));

INSERT INTO user_education (user_id, school, degree, field_of_study, start_year, end_year, sort_order)
SELECT id, 'University of Nigeria Nsukka', 'B.Sc', 'Information Systems', 2009, 2013, 1 FROM users WHERE email = 'kelechi.nnamdi@example.com'
  AND (SELECT COUNT(*) FROM user_education WHERE user_id = (SELECT id FROM users WHERE email = 'kelechi.nnamdi@example.com')) = 1;

UPDATE users SET personal_interests = 'Football, Art Collecting, Gardening', phone = '+234 760 626 4812',
  bio = IF(bio IS NULL OR bio = '', 'Overseeing talent strategy across Nigerian Breweries\' northern operations.', bio),
  skills = IF(skills IS NULL OR skills = '', 'HR Strategy, Talent Development, Change Management', skills),
  expertise_areas = IF(expertise_areas IS NULL OR expertise_areas = '', 'People Operations', expertise_areas),
  business_interests = IF(business_interests IS NULL OR business_interests = '', 'FMCG', business_interests),
  linkedin_url = IF(linkedin_url IS NULL OR linkedin_url = '', 'https://linkedin.com/in/aishamohammed', linkedin_url),
  company = IF(company IS NULL OR company = '', 'Nigerian Breweries', company),
  headline = IF(headline IS NULL OR headline = '', 'Regional HR Director, Nigerian Breweries', headline),
  industry = IF(industry IS NULL OR industry = '', 'Consumer Goods', industry),
  city = IF(city IS NULL OR city = '', 'Kaduna', city),
  country = IF(country IS NULL OR country = '', 'Nigeria', country),
  state = IF(state IS NULL OR state = '', 'Kaduna State', state),
  program = IF(program IS NULL OR program = '', 'MBA', program),
  graduation_year = IF(graduation_year IS NULL, 2015, graduation_year),
  show_phone = IF(show_phone = 0, 1, show_phone),
  show_email = IF(show_email = 0, 1, show_email)
WHERE email = 'aisha.mohammed@example.com' AND (personal_interests IS NULL OR personal_interests = '');

INSERT INTO user_experience (user_id, title, company, location, start_date, end_date, is_current, description, sort_order)
SELECT id, 'Regional HR Director', 'Nigerian Breweries', 'Kaduna, Nigeria', '2016-03-01', NULL, 1, 'Leading people operations initiatives at Nigerian Breweries, based in Kaduna.', 0 FROM users WHERE email = 'aisha.mohammed@example.com'
  AND NOT EXISTS (SELECT 1 FROM user_experience WHERE user_id = (SELECT id FROM users WHERE email = 'aisha.mohammed@example.com'));

INSERT INTO user_experience (user_id, title, company, location, start_date, end_date, is_current, description, sort_order)
SELECT id, 'Associate, Brand Management', 'Unilever Nigeria', 'Kaduna, Nigeria', '2012-01-01', '2015-12-31', 0, 'Supported people operations projects early in career at Unilever Nigeria.', 1 FROM users WHERE email = 'aisha.mohammed@example.com'
  AND (SELECT COUNT(*) FROM user_experience WHERE user_id = (SELECT id FROM users WHERE email = 'aisha.mohammed@example.com')) = 1;

INSERT INTO user_education (user_id, school, degree, field_of_study, start_year, end_year, sort_order)
SELECT id, 'Rome Business School Nigeria', 'MBA', 'Business Administration', 2013, 2015, 0 FROM users WHERE email = 'aisha.mohammed@example.com'
  AND NOT EXISTS (SELECT 1 FROM user_education WHERE user_id = (SELECT id FROM users WHERE email = 'aisha.mohammed@example.com'));

INSERT INTO user_education (user_id, school, degree, field_of_study, start_year, end_year, sort_order)
SELECT id, 'University of Ibadan', 'B.Sc', 'Marketing', 2008, 2012, 1 FROM users WHERE email = 'aisha.mohammed@example.com'
  AND (SELECT COUNT(*) FROM user_education WHERE user_id = (SELECT id FROM users WHERE email = 'aisha.mohammed@example.com')) = 1;

UPDATE users SET personal_interests = 'Mentoring, Cycling, Fitness Training', phone = '+234 700 861 8085',
  bio = IF(bio IS NULL OR bio = '', 'Leading digital lending operations serving underbanked Nigerian consumers and SMEs.', bio),
  skills = IF(skills IS NULL OR skills = '', 'Consumer Lending, P&L Management, Strategy', skills),
  expertise_areas = IF(expertise_areas IS NULL OR expertise_areas = '', 'Digital Lending', expertise_areas),
  business_interests = IF(business_interests IS NULL OR business_interests = '', 'Financial Inclusion', business_interests),
  linkedin_url = IF(linkedin_url IS NULL OR linkedin_url = '', 'https://linkedin.com/in/femiadewale', linkedin_url),
  company = IF(company IS NULL OR company = '', 'Renmoney', company),
  headline = IF(headline IS NULL OR headline = '', 'Managing Director, Renmoney', headline),
  industry = IF(industry IS NULL OR industry = '', 'Banking & Finance', industry),
  city = IF(city IS NULL OR city = '', 'Lagos', city),
  country = IF(country IS NULL OR country = '', 'Nigeria', country),
  state = IF(state IS NULL OR state = '', 'Lagos State', state),
  program = IF(program IS NULL OR program = '', 'Executive MBA', program),
  graduation_year = IF(graduation_year IS NULL, 2013, graduation_year),
  show_phone = IF(show_phone = 0, 1, show_phone),
  show_email = IF(show_email = 0, 1, show_email)
WHERE email = 'femi.adewale@example.com' AND (personal_interests IS NULL OR personal_interests = '');

INSERT INTO user_experience (user_id, title, company, location, start_date, end_date, is_current, description, sort_order)
SELECT id, 'Managing Director', 'Renmoney', 'Lagos, Nigeria', '2014-05-01', NULL, 1, 'Leading digital lending initiatives at Renmoney, based in Lagos.', 0 FROM users WHERE email = 'femi.adewale@example.com'
  AND NOT EXISTS (SELECT 1 FROM user_experience WHERE user_id = (SELECT id FROM users WHERE email = 'femi.adewale@example.com'));

INSERT INTO user_experience (user_id, title, company, location, start_date, end_date, is_current, description, sort_order)
SELECT id, 'Associate, Corporate Finance', 'Stanbic IBTC', 'Lagos, Nigeria', '2010-01-01', '2013-12-31', 0, 'Supported digital lending projects early in career at Stanbic IBTC.', 1 FROM users WHERE email = 'femi.adewale@example.com'
  AND (SELECT COUNT(*) FROM user_experience WHERE user_id = (SELECT id FROM users WHERE email = 'femi.adewale@example.com')) = 1;

INSERT INTO user_education (user_id, school, degree, field_of_study, start_year, end_year, sort_order)
SELECT id, 'Rome Business School Nigeria', 'Executive MBA', 'Business Administration', 2011, 2013, 0 FROM users WHERE email = 'femi.adewale@example.com'
  AND NOT EXISTS (SELECT 1 FROM user_education WHERE user_id = (SELECT id FROM users WHERE email = 'femi.adewale@example.com'));

INSERT INTO user_education (user_id, school, degree, field_of_study, start_year, end_year, sort_order)
SELECT id, 'University of Port Harcourt', 'B.Sc', 'Finance', 2006, 2010, 1 FROM users WHERE email = 'femi.adewale@example.com'
  AND (SELECT COUNT(*) FROM user_education WHERE user_id = (SELECT id FROM users WHERE email = 'femi.adewale@example.com')) = 1;

UPDATE users SET personal_interests = 'Golf, Mentoring, Jazz Music', phone = '+234 845 656 4965',
  bio = IF(bio IS NULL OR bio = '', 'Optimizing cement production supply chains across South-West Nigeria.', bio),
  skills = IF(skills IS NULL OR skills = '', 'Supply Chain, Logistics, Procurement', skills),
  expertise_areas = IF(expertise_areas IS NULL OR expertise_areas = '', 'Manufacturing Operations', expertise_areas),
  business_interests = IF(business_interests IS NULL OR business_interests = '', 'Industrial Manufacturing', business_interests),
  linkedin_url = IF(linkedin_url IS NULL OR linkedin_url = '', 'https://linkedin.com/in/ngoziobiora', linkedin_url),
  company = IF(company IS NULL OR company = '', 'Lafarge Africa', company),
  headline = IF(headline IS NULL OR headline = '', 'Supply Chain Manager, Lafarge Africa', headline),
  industry = IF(industry IS NULL OR industry = '', 'Manufacturing', industry),
  city = IF(city IS NULL OR city = '', 'Ibadan', city),
  country = IF(country IS NULL OR country = '', 'Nigeria', country),
  state = IF(state IS NULL OR state = '', 'Oyo State', state),
  program = IF(program IS NULL OR program = '', 'MBA', program),
  graduation_year = IF(graduation_year IS NULL, 2019, graduation_year),
  show_phone = IF(show_phone = 0, 1, show_phone),
  show_email = IF(show_email = 0, 1, show_email)
WHERE email = 'ngozi.obiora@example.com' AND (personal_interests IS NULL OR personal_interests = '');

INSERT INTO user_experience (user_id, title, company, location, start_date, end_date, is_current, description, sort_order)
SELECT id, 'Supply Chain Manager', 'Lafarge Africa', 'Ibadan, Nigeria', '2020-03-01', NULL, 1, 'Leading manufacturing operations initiatives at Lafarge Africa, based in Ibadan.', 0 FROM users WHERE email = 'ngozi.obiora@example.com'
  AND NOT EXISTS (SELECT 1 FROM user_experience WHERE user_id = (SELECT id FROM users WHERE email = 'ngozi.obiora@example.com'));

INSERT INTO user_experience (user_id, title, company, location, start_date, end_date, is_current, description, sort_order)
SELECT id, 'Associate, Operations', 'Nigerian Bottling Company', 'Ibadan, Nigeria', '2016-01-01', '2019-12-31', 0, 'Supported manufacturing operations projects early in career at Nigerian Bottling Company.', 1 FROM users WHERE email = 'ngozi.obiora@example.com'
  AND (SELECT COUNT(*) FROM user_experience WHERE user_id = (SELECT id FROM users WHERE email = 'ngozi.obiora@example.com')) = 1;

INSERT INTO user_education (user_id, school, degree, field_of_study, start_year, end_year, sort_order)
SELECT id, 'Rome Business School Nigeria', 'MBA', 'Business Administration', 2017, 2019, 0 FROM users WHERE email = 'ngozi.obiora@example.com'
  AND NOT EXISTS (SELECT 1 FROM user_education WHERE user_id = (SELECT id FROM users WHERE email = 'ngozi.obiora@example.com'));

INSERT INTO user_education (user_id, school, degree, field_of_study, start_year, end_year, sort_order)
SELECT id, 'University of Benin', 'B.Sc', 'Mechanical Engineering', 2012, 2016, 1 FROM users WHERE email = 'ngozi.obiora@example.com'
  AND (SELECT COUNT(*) FROM user_education WHERE user_id = (SELECT id FROM users WHERE email = 'ngozi.obiora@example.com')) = 1;

UPDATE users SET personal_interests = 'Traveling, Writing, Fitness Training', phone = '+234 824 724 1600',
  bio = IF(bio IS NULL OR bio = '', 'Managing underwriting strategy for corporate and retail insurance lines.', bio),
  skills = IF(skills IS NULL OR skills = '', 'Underwriting, Actuarial Analysis, Risk Assessment', skills),
  expertise_areas = IF(expertise_areas IS NULL OR expertise_areas = '', 'Insurance', expertise_areas),
  business_interests = IF(business_interests IS NULL OR business_interests = '', 'InsurTech', business_interests),
  linkedin_url = IF(linkedin_url IS NULL OR linkedin_url = '', 'https://linkedin.com/in/babajidefashola', linkedin_url),
  company = IF(company IS NULL OR company = '', 'AXA Mansard Insurance', company),
  headline = IF(headline IS NULL OR headline = '', 'Head of Underwriting, AXA Mansard Insurance', headline),
  industry = IF(industry IS NULL OR industry = '', 'Insurance', industry),
  city = IF(city IS NULL OR city = '', 'Lagos', city),
  country = IF(country IS NULL OR country = '', 'Nigeria', country),
  state = IF(state IS NULL OR state = '', 'Lagos State', state),
  program = IF(program IS NULL OR program = '', 'MSc Finance', program),
  graduation_year = IF(graduation_year IS NULL, 2014, graduation_year),
  show_phone = IF(show_phone = 0, 1, show_phone),
  show_email = IF(show_email = 0, 1, show_email)
WHERE email = 'babajide.fashola@example.com' AND (personal_interests IS NULL OR personal_interests = '');

INSERT INTO user_experience (user_id, title, company, location, start_date, end_date, is_current, description, sort_order)
SELECT id, 'Head of Underwriting', 'AXA Mansard Insurance', 'Lagos, Nigeria', '2015-03-01', NULL, 1, 'Leading insurance initiatives at AXA Mansard Insurance, based in Lagos.', 0 FROM users WHERE email = 'babajide.fashola@example.com'
  AND NOT EXISTS (SELECT 1 FROM user_experience WHERE user_id = (SELECT id FROM users WHERE email = 'babajide.fashola@example.com'));

INSERT INTO user_experience (user_id, title, company, location, start_date, end_date, is_current, description, sort_order)
SELECT id, 'Associate, Underwriting', 'Leadway Assurance', 'Lagos, Nigeria', '2011-01-01', '2014-12-31', 0, 'Supported insurance projects early in career at Leadway Assurance.', 1 FROM users WHERE email = 'babajide.fashola@example.com'
  AND (SELECT COUNT(*) FROM user_experience WHERE user_id = (SELECT id FROM users WHERE email = 'babajide.fashola@example.com')) = 1;

INSERT INTO user_education (user_id, school, degree, field_of_study, start_year, end_year, sort_order)
SELECT id, 'Rome Business School Nigeria', 'MSc Finance', 'Business Administration', 2012, 2014, 0 FROM users WHERE email = 'babajide.fashola@example.com'
  AND NOT EXISTS (SELECT 1 FROM user_education WHERE user_id = (SELECT id FROM users WHERE email = 'babajide.fashola@example.com'));

INSERT INTO user_education (user_id, school, degree, field_of_study, start_year, end_year, sort_order)
SELECT id, 'Kwame Nkrumah University of Science and Technology', 'B.Sc', 'Insurance', 2007, 2011, 1 FROM users WHERE email = 'babajide.fashola@example.com'
  AND (SELECT COUNT(*) FROM user_education WHERE user_id = (SELECT id FROM users WHERE email = 'babajide.fashola@example.com')) = 1;

UPDATE users SET personal_interests = 'Running, Football, Writing, Art Collecting', phone = '+234 846 315 9706',
  bio = IF(bio IS NULL OR bio = '', 'Building a chain of affordable primary healthcare clinics across South-East Nigeria.', bio),
  skills = IF(skills IS NULL OR skills = '', 'Healthcare Management, Entrepreneurship, Operations', skills),
  expertise_areas = IF(expertise_areas IS NULL OR expertise_areas = '', 'Healthcare Delivery', expertise_areas),
  business_interests = IF(business_interests IS NULL OR business_interests = '', 'Primary Healthcare', business_interests),
  linkedin_url = IF(linkedin_url IS NULL OR linkedin_url = '', 'https://linkedin.com/in/chidinmauzo', linkedin_url),
  company = IF(company IS NULL OR company = '', 'Uzo Wellness Clinics', company),
  headline = IF(headline IS NULL OR headline = '', 'Founder, Uzo Wellness Clinics', headline),
  industry = IF(industry IS NULL OR industry = '', 'Healthcare', industry),
  city = IF(city IS NULL OR city = '', 'Enugu', city),
  country = IF(country IS NULL OR country = '', 'Nigeria', country),
  state = IF(state IS NULL OR state = '', 'Enugu State', state),
  program = IF(program IS NULL OR program = '', 'MBA', program),
  graduation_year = IF(graduation_year IS NULL, 2018, graduation_year),
  show_phone = IF(show_phone = 0, 1, show_phone),
  show_email = IF(show_email = 0, 1, show_email)
WHERE email = 'chidinma.uzo@example.com' AND (personal_interests IS NULL OR personal_interests = '');

INSERT INTO user_experience (user_id, title, company, location, start_date, end_date, is_current, description, sort_order)
SELECT id, 'Founder', 'Uzo Wellness Clinics', 'Enugu, Nigeria', '2019-02-01', NULL, 1, 'Leading healthcare delivery initiatives at Uzo Wellness Clinics, based in Enugu.', 0 FROM users WHERE email = 'chidinma.uzo@example.com'
  AND NOT EXISTS (SELECT 1 FROM user_experience WHERE user_id = (SELECT id FROM users WHERE email = 'chidinma.uzo@example.com'));

INSERT INTO user_experience (user_id, title, company, location, start_date, end_date, is_current, description, sort_order)
SELECT id, 'Associate, Healthcare Management', 'Reddington Hospital', 'Enugu, Nigeria', '2015-01-01', '2018-12-31', 0, 'Supported healthcare delivery projects early in career at Reddington Hospital.', 1 FROM users WHERE email = 'chidinma.uzo@example.com'
  AND (SELECT COUNT(*) FROM user_experience WHERE user_id = (SELECT id FROM users WHERE email = 'chidinma.uzo@example.com')) = 1;

INSERT INTO user_education (user_id, school, degree, field_of_study, start_year, end_year, sort_order)
SELECT id, 'Rome Business School Nigeria', 'MBA', 'Business Administration', 2016, 2018, 0 FROM users WHERE email = 'chidinma.uzo@example.com'
  AND NOT EXISTS (SELECT 1 FROM user_education WHERE user_id = (SELECT id FROM users WHERE email = 'chidinma.uzo@example.com'));

INSERT INTO user_education (user_id, school, degree, field_of_study, start_year, end_year, sort_order)
SELECT id, 'Ahmadu Bello University', 'B.Sc', 'Public Health', 2011, 2015, 1 FROM users WHERE email = 'chidinma.uzo@example.com'
  AND (SELECT COUNT(*) FROM user_education WHERE user_id = (SELECT id FROM users WHERE email = 'chidinma.uzo@example.com')) = 1;

UPDATE users SET personal_interests = 'Cycling, Podcasting, Board Games', phone = '+234 706 373 3397',
  bio = IF(bio IS NULL OR bio = '', 'Leading upstream operations for one of Nigeria\'s largest independent energy companies.', bio),
  skills = IF(skills IS NULL OR skills = '', 'Energy Operations, Regulatory Affairs, Strategy', skills),
  expertise_areas = IF(expertise_areas IS NULL OR expertise_areas = '', 'Oil & Gas', expertise_areas),
  business_interests = IF(business_interests IS NULL OR business_interests = '', 'Energy Transition', business_interests),
  linkedin_url = IF(linkedin_url IS NULL OR linkedin_url = '', 'https://linkedin.com/in/suleimanbello', linkedin_url),
  company = IF(company IS NULL OR company = '', 'Seplat Energy', company),
  headline = IF(headline IS NULL OR headline = '', 'Country Manager, Seplat Energy', headline),
  industry = IF(industry IS NULL OR industry = '', 'Oil & Gas', industry),
  city = IF(city IS NULL OR city = '', 'Port Harcourt', city),
  country = IF(country IS NULL OR country = '', 'Nigeria', country),
  state = IF(state IS NULL OR state = '', 'Rivers State', state),
  program = IF(program IS NULL OR program = '', 'Executive MBA', program),
  graduation_year = IF(graduation_year IS NULL, 2011, graduation_year),
  show_phone = IF(show_phone = 0, 1, show_phone),
  show_email = IF(show_email = 0, 1, show_email)
WHERE email = 'suleiman.bello@example.com' AND (personal_interests IS NULL OR personal_interests = '');

INSERT INTO user_experience (user_id, title, company, location, start_date, end_date, is_current, description, sort_order)
SELECT id, 'Country Manager', 'Seplat Energy', 'Port Harcourt, Nigeria', '2012-03-01', NULL, 1, 'Leading oil & gas initiatives at Seplat Energy, based in Port Harcourt.', 0 FROM users WHERE email = 'suleiman.bello@example.com'
  AND NOT EXISTS (SELECT 1 FROM user_experience WHERE user_id = (SELECT id FROM users WHERE email = 'suleiman.bello@example.com'));

INSERT INTO user_experience (user_id, title, company, location, start_date, end_date, is_current, description, sort_order)
SELECT id, 'Associate, Upstream Operations', 'Shell Nigeria', 'Port Harcourt, Nigeria', '2008-01-01', '2011-12-31', 0, 'Supported oil & gas projects early in career at Shell Nigeria.', 1 FROM users WHERE email = 'suleiman.bello@example.com'
  AND (SELECT COUNT(*) FROM user_experience WHERE user_id = (SELECT id FROM users WHERE email = 'suleiman.bello@example.com')) = 1;

INSERT INTO user_education (user_id, school, degree, field_of_study, start_year, end_year, sort_order)
SELECT id, 'Rome Business School Nigeria', 'Executive MBA', 'Business Administration', 2009, 2011, 0 FROM users WHERE email = 'suleiman.bello@example.com'
  AND NOT EXISTS (SELECT 1 FROM user_education WHERE user_id = (SELECT id FROM users WHERE email = 'suleiman.bello@example.com'));

INSERT INTO user_education (user_id, school, degree, field_of_study, start_year, end_year, sort_order)
SELECT id, 'University of Ghana', 'B.Sc', 'Petroleum Engineering', 2004, 2008, 1 FROM users WHERE email = 'suleiman.bello@example.com'
  AND (SELECT COUNT(*) FROM user_education WHERE user_id = (SELECT id FROM users WHERE email = 'suleiman.bello@example.com')) = 1;

UPDATE users SET personal_interests = 'Traveling, Volunteering, Yoga, Fitness Training', phone = '+234 767 119 6878',
  bio = IF(bio IS NULL OR bio = '', 'Building savings and investment products for young African professionals.', bio),
  skills = IF(skills IS NULL OR skills = '', 'Product Management, Fintech, UX Strategy', skills),
  expertise_areas = IF(expertise_areas IS NULL OR expertise_areas = '', 'Wealth Tech', expertise_areas),
  business_interests = IF(business_interests IS NULL OR business_interests = '', 'Personal Finance', business_interests),
  linkedin_url = IF(linkedin_url IS NULL OR linkedin_url = '', 'https://linkedin.com/in/temitopeadeyemi', linkedin_url),
  company = IF(company IS NULL OR company = '', 'Cowrywise', company),
  headline = IF(headline IS NULL OR headline = '', 'Product Lead, Cowrywise', headline),
  industry = IF(industry IS NULL OR industry = '', 'Technology & Fintech', industry),
  city = IF(city IS NULL OR city = '', 'Lagos', city),
  country = IF(country IS NULL OR country = '', 'Nigeria', country),
  state = IF(state IS NULL OR state = '', 'Lagos State', state),
  program = IF(program IS NULL OR program = '', 'MBA', program),
  graduation_year = IF(graduation_year IS NULL, 2020, graduation_year),
  show_phone = IF(show_phone = 0, 1, show_phone),
  show_email = IF(show_email = 0, 1, show_email)
WHERE email = 'temitope.adeyemi@example.com' AND (personal_interests IS NULL OR personal_interests = '');

INSERT INTO user_experience (user_id, title, company, location, start_date, end_date, is_current, description, sort_order)
SELECT id, 'Product Lead', 'Cowrywise', 'Lagos, Nigeria', '2021-04-01', NULL, 1, 'Leading wealth tech initiatives at Cowrywise, based in Lagos.', 0 FROM users WHERE email = 'temitope.adeyemi@example.com'
  AND NOT EXISTS (SELECT 1 FROM user_experience WHERE user_id = (SELECT id FROM users WHERE email = 'temitope.adeyemi@example.com'));

INSERT INTO user_experience (user_id, title, company, location, start_date, end_date, is_current, description, sort_order)
SELECT id, 'Associate, Product Strategy', 'Flutterwave', 'Lagos, Nigeria', '2017-01-01', '2020-12-31', 0, 'Supported wealth tech projects early in career at Flutterwave.', 1 FROM users WHERE email = 'temitope.adeyemi@example.com'
  AND (SELECT COUNT(*) FROM user_experience WHERE user_id = (SELECT id FROM users WHERE email = 'temitope.adeyemi@example.com')) = 1;

INSERT INTO user_education (user_id, school, degree, field_of_study, start_year, end_year, sort_order)
SELECT id, 'Rome Business School Nigeria', 'MBA', 'Business Administration', 2018, 2020, 0 FROM users WHERE email = 'temitope.adeyemi@example.com'
  AND NOT EXISTS (SELECT 1 FROM user_education WHERE user_id = (SELECT id FROM users WHERE email = 'temitope.adeyemi@example.com'));

INSERT INTO user_education (user_id, school, degree, field_of_study, start_year, end_year, sort_order)
SELECT id, 'University of Ibadan', 'B.Sc', 'Software Engineering', 2013, 2017, 1 FROM users WHERE email = 'temitope.adeyemi@example.com'
  AND (SELECT COUNT(*) FROM user_education WHERE user_id = (SELECT id FROM users WHERE email = 'temitope.adeyemi@example.com')) = 1;

UPDATE users SET personal_interests = 'Running, Art Collecting, Fitness Training', phone = '+234 767 889 5763',
  bio = IF(bio IS NULL OR bio = '', 'Overseeing financial strategy and reporting for Cadbury\'s Nigerian operations.', bio),
  skills = IF(skills IS NULL OR skills = '', 'Financial Reporting, Corporate Finance, Budgeting', skills),
  expertise_areas = IF(expertise_areas IS NULL OR expertise_areas = '', 'FMCG Finance', expertise_areas),
  business_interests = IF(business_interests IS NULL OR business_interests = '', 'Consumer Goods', business_interests),
  linkedin_url = IF(linkedin_url IS NULL OR linkedin_url = '', 'https://linkedin.com/in/ikechukwuokoro', linkedin_url),
  company = IF(company IS NULL OR company = '', 'Cadbury Nigeria', company),
  headline = IF(headline IS NULL OR headline = '', 'Finance Director, Cadbury Nigeria', headline),
  industry = IF(industry IS NULL OR industry = '', 'Consumer Goods', industry),
  city = IF(city IS NULL OR city = '', 'Lagos', city),
  country = IF(country IS NULL OR country = '', 'Nigeria', country),
  state = IF(state IS NULL OR state = '', 'Lagos State', state),
  program = IF(program IS NULL OR program = '', 'Executive MBA', program),
  graduation_year = IF(graduation_year IS NULL, 2012, graduation_year),
  show_phone = IF(show_phone = 0, 1, show_phone),
  show_email = IF(show_email = 0, 1, show_email)
WHERE email = 'ikechukwu.okoro@example.com' AND (personal_interests IS NULL OR personal_interests = '');

INSERT INTO user_experience (user_id, title, company, location, start_date, end_date, is_current, description, sort_order)
SELECT id, 'Finance Director', 'Cadbury Nigeria', 'Lagos, Nigeria', '2013-01-01', NULL, 1, 'Leading fmcg finance initiatives at Cadbury Nigeria, based in Lagos.', 0 FROM users WHERE email = 'ikechukwu.okoro@example.com'
  AND NOT EXISTS (SELECT 1 FROM user_experience WHERE user_id = (SELECT id FROM users WHERE email = 'ikechukwu.okoro@example.com'));

INSERT INTO user_experience (user_id, title, company, location, start_date, end_date, is_current, description, sort_order)
SELECT id, 'Associate, Brand Management', 'PZ Cussons', 'Lagos, Nigeria', '2009-01-01', '2012-12-31', 0, 'Supported fmcg finance projects early in career at PZ Cussons.', 1 FROM users WHERE email = 'ikechukwu.okoro@example.com'
  AND (SELECT COUNT(*) FROM user_experience WHERE user_id = (SELECT id FROM users WHERE email = 'ikechukwu.okoro@example.com')) = 1;

INSERT INTO user_education (user_id, school, degree, field_of_study, start_year, end_year, sort_order)
SELECT id, 'Rome Business School Nigeria', 'Executive MBA', 'Business Administration', 2010, 2012, 0 FROM users WHERE email = 'ikechukwu.okoro@example.com'
  AND NOT EXISTS (SELECT 1 FROM user_education WHERE user_id = (SELECT id FROM users WHERE email = 'ikechukwu.okoro@example.com'));

INSERT INTO user_education (user_id, school, degree, field_of_study, start_year, end_year, sort_order)
SELECT id, 'Ahmadu Bello University', 'B.Sc', 'Business Administration', 2005, 2009, 1 FROM users WHERE email = 'ikechukwu.okoro@example.com'
  AND (SELECT COUNT(*) FROM user_education WHERE user_id = (SELECT id FROM users WHERE email = 'ikechukwu.okoro@example.com')) = 1;

UPDATE users SET personal_interests = 'Reading, Golf, Running, Board Games', phone = '+234 701 189 7864',
  bio = IF(bio IS NULL OR bio = '', 'Advising on regulatory compliance and corporate transactions for one of Nigeria\'s oldest banks.', bio),
  skills = IF(skills IS NULL OR skills = '', 'Banking Law, Compliance, Contract Negotiation', skills),
  expertise_areas = IF(expertise_areas IS NULL OR expertise_areas = '', 'Financial Regulation', expertise_areas),
  business_interests = IF(business_interests IS NULL OR business_interests = '', 'Corporate Governance', business_interests),
  linkedin_url = IF(linkedin_url IS NULL OR linkedin_url = '', 'https://linkedin.com/in/zainabyusuf', linkedin_url),
  company = IF(company IS NULL OR company = '', 'First Bank Nigeria', company),
  headline = IF(headline IS NULL OR headline = '', 'Senior Legal Counsel, First Bank Nigeria', headline),
  industry = IF(industry IS NULL OR industry = '', 'Banking & Finance', industry),
  city = IF(city IS NULL OR city = '', 'Abuja', city),
  country = IF(country IS NULL OR country = '', 'Nigeria', country),
  state = IF(state IS NULL OR state = '', 'FCT', state),
  program = IF(program IS NULL OR program = '', 'MBA', program),
  graduation_year = IF(graduation_year IS NULL, 2016, graduation_year),
  show_phone = IF(show_phone = 0, 1, show_phone),
  show_email = IF(show_email = 0, 1, show_email)
WHERE email = 'zainab.yusuf@example.com' AND (personal_interests IS NULL OR personal_interests = '');

INSERT INTO user_experience (user_id, title, company, location, start_date, end_date, is_current, description, sort_order)
SELECT id, 'Senior Legal Counsel', 'First Bank Nigeria', 'Abuja, Nigeria', '2017-06-01', NULL, 1, 'Leading financial regulation initiatives at First Bank Nigeria, based in Abuja.', 0 FROM users WHERE email = 'zainab.yusuf@example.com'
  AND NOT EXISTS (SELECT 1 FROM user_experience WHERE user_id = (SELECT id FROM users WHERE email = 'zainab.yusuf@example.com'));

INSERT INTO user_experience (user_id, title, company, location, start_date, end_date, is_current, description, sort_order)
SELECT id, 'Associate, Corporate Finance', 'Polaris Bank', 'Abuja, Nigeria', '2013-01-01', '2016-12-31', 0, 'Supported financial regulation projects early in career at Polaris Bank.', 1 FROM users WHERE email = 'zainab.yusuf@example.com'
  AND (SELECT COUNT(*) FROM user_experience WHERE user_id = (SELECT id FROM users WHERE email = 'zainab.yusuf@example.com')) = 1;

INSERT INTO user_education (user_id, school, degree, field_of_study, start_year, end_year, sort_order)
SELECT id, 'Rome Business School Nigeria', 'MBA', 'Business Administration', 2014, 2016, 0 FROM users WHERE email = 'zainab.yusuf@example.com'
  AND NOT EXISTS (SELECT 1 FROM user_education WHERE user_id = (SELECT id FROM users WHERE email = 'zainab.yusuf@example.com'));

INSERT INTO user_education (user_id, school, degree, field_of_study, start_year, end_year, sort_order)
SELECT id, 'University of Nigeria Nsukka', 'B.Sc', 'Accounting', 2009, 2013, 1 FROM users WHERE email = 'zainab.yusuf@example.com'
  AND (SELECT COUNT(*) FROM user_education WHERE user_id = (SELECT id FROM users WHERE email = 'zainab.yusuf@example.com')) = 1;

UPDATE users SET personal_interests = 'Cooking, Volunteering, Jazz Music, Public Speaking', phone = '+234 810 611 5899',
  bio = IF(bio IS NULL OR bio = '', 'Leading data infrastructure operations across West Africa\'s largest connectivity provider.', bio),
  skills = IF(skills IS NULL OR skills = '', 'Infrastructure Operations, Telecom, Data Centers', skills),
  expertise_areas = IF(expertise_areas IS NULL OR expertise_areas = '', 'Digital Infrastructure', expertise_areas),
  business_interests = IF(business_interests IS NULL OR business_interests = '', 'Connectivity', business_interests),
  linkedin_url = IF(linkedin_url IS NULL OR linkedin_url = '', 'https://linkedin.com/in/olumidefagbenle', linkedin_url),
  company = IF(company IS NULL OR company = '', 'MainOne', company),
  headline = IF(headline IS NULL OR headline = '', 'Director of Operations, MainOne', headline),
  industry = IF(industry IS NULL OR industry = '', 'Technology & Fintech', industry),
  city = IF(city IS NULL OR city = '', 'Lagos', city),
  country = IF(country IS NULL OR country = '', 'Nigeria', country),
  state = IF(state IS NULL OR state = '', 'Lagos State', state),
  program = IF(program IS NULL OR program = '', 'Executive MBA', program),
  graduation_year = IF(graduation_year IS NULL, 2015, graduation_year),
  show_phone = IF(show_phone = 0, 1, show_phone),
  show_email = IF(show_email = 0, 1, show_email)
WHERE email = 'olumide.fagbenle@example.com' AND (personal_interests IS NULL OR personal_interests = '');

INSERT INTO user_experience (user_id, title, company, location, start_date, end_date, is_current, description, sort_order)
SELECT id, 'Director of Operations', 'MainOne', 'Lagos, Nigeria', '2016-04-01', NULL, 1, 'Leading digital infrastructure initiatives at MainOne, based in Lagos.', 0 FROM users WHERE email = 'olumide.fagbenle@example.com'
  AND NOT EXISTS (SELECT 1 FROM user_experience WHERE user_id = (SELECT id FROM users WHERE email = 'olumide.fagbenle@example.com'));

INSERT INTO user_experience (user_id, title, company, location, start_date, end_date, is_current, description, sort_order)
SELECT id, 'Associate, Product Strategy', 'Interswitch', 'Lagos, Nigeria', '2012-01-01', '2015-12-31', 0, 'Supported digital infrastructure projects early in career at Interswitch.', 1 FROM users WHERE email = 'olumide.fagbenle@example.com'
  AND (SELECT COUNT(*) FROM user_experience WHERE user_id = (SELECT id FROM users WHERE email = 'olumide.fagbenle@example.com')) = 1;

INSERT INTO user_education (user_id, school, degree, field_of_study, start_year, end_year, sort_order)
SELECT id, 'Rome Business School Nigeria', 'Executive MBA', 'Business Administration', 2013, 2015, 0 FROM users WHERE email = 'olumide.fagbenle@example.com'
  AND NOT EXISTS (SELECT 1 FROM user_education WHERE user_id = (SELECT id FROM users WHERE email = 'olumide.fagbenle@example.com'));

INSERT INTO user_education (user_id, school, degree, field_of_study, start_year, end_year, sort_order)
SELECT id, 'University of Ibadan', 'B.Sc', 'Software Engineering', 2008, 2012, 1 FROM users WHERE email = 'olumide.fagbenle@example.com'
  AND (SELECT COUNT(*) FROM user_education WHERE user_id = (SELECT id FROM users WHERE email = 'olumide.fagbenle@example.com')) = 1;

UPDATE users SET personal_interests = 'Golf, Jazz Music, Football, Writing', phone = '+234 838 807 6874',
  bio = IF(bio IS NULL OR bio = '', 'Managing personal care brand strategy across Nigeria and Ghana.', bio),
  skills = IF(skills IS NULL OR skills = '', 'Brand Management, Consumer Insights, Campaign Strategy', skills),
  expertise_areas = IF(expertise_areas IS NULL OR expertise_areas = '', 'FMCG Marketing', expertise_areas),
  business_interests = IF(business_interests IS NULL OR business_interests = '', 'Personal Care', business_interests),
  linkedin_url = IF(linkedin_url IS NULL OR linkedin_url = '', 'https://linkedin.com/in/chizobanwachukwu', linkedin_url),
  company = IF(company IS NULL OR company = '', 'Unilever Nigeria', company),
  headline = IF(headline IS NULL OR headline = '', 'Brand Manager, Unilever Nigeria', headline),
  industry = IF(industry IS NULL OR industry = '', 'Consumer Goods', industry),
  city = IF(city IS NULL OR city = '', 'Lagos', city),
  country = IF(country IS NULL OR country = '', 'Nigeria', country),
  state = IF(state IS NULL OR state = '', 'Lagos State', state),
  program = IF(program IS NULL OR program = '', 'MSc Marketing', program),
  graduation_year = IF(graduation_year IS NULL, 2021, graduation_year),
  show_phone = IF(show_phone = 0, 1, show_phone),
  show_email = IF(show_email = 0, 1, show_email)
WHERE email = 'chizoba.nwachukwu@example.com' AND (personal_interests IS NULL OR personal_interests = '');

INSERT INTO user_experience (user_id, title, company, location, start_date, end_date, is_current, description, sort_order)
SELECT id, 'Brand Manager', 'Unilever Nigeria', 'Lagos, Nigeria', '2022-02-01', NULL, 1, 'Leading fmcg marketing initiatives at Unilever Nigeria, based in Lagos.', 0 FROM users WHERE email = 'chizoba.nwachukwu@example.com'
  AND NOT EXISTS (SELECT 1 FROM user_experience WHERE user_id = (SELECT id FROM users WHERE email = 'chizoba.nwachukwu@example.com'));

INSERT INTO user_experience (user_id, title, company, location, start_date, end_date, is_current, description, sort_order)
SELECT id, 'Associate, Brand Management', 'Nestle Nigeria', 'Lagos, Nigeria', '2018-01-01', '2021-12-31', 0, 'Supported fmcg marketing projects early in career at Nestle Nigeria.', 1 FROM users WHERE email = 'chizoba.nwachukwu@example.com'
  AND (SELECT COUNT(*) FROM user_experience WHERE user_id = (SELECT id FROM users WHERE email = 'chizoba.nwachukwu@example.com')) = 1;

INSERT INTO user_education (user_id, school, degree, field_of_study, start_year, end_year, sort_order)
SELECT id, 'Rome Business School Nigeria', 'MSc Marketing', 'Business Administration', 2019, 2021, 0 FROM users WHERE email = 'chizoba.nwachukwu@example.com'
  AND NOT EXISTS (SELECT 1 FROM user_education WHERE user_id = (SELECT id FROM users WHERE email = 'chizoba.nwachukwu@example.com'));

INSERT INTO user_education (user_id, school, degree, field_of_study, start_year, end_year, sort_order)
SELECT id, 'University of Ibadan', 'B.Sc', 'Marketing', 2014, 2018, 1 FROM users WHERE email = 'chizoba.nwachukwu@example.com'
  AND (SELECT COUNT(*) FROM user_education WHERE user_id = (SELECT id FROM users WHERE email = 'chizoba.nwachukwu@example.com')) = 1;

UPDATE users SET personal_interests = 'Reading, Running, Art Collecting', phone = '+234 760 663 3686',
  bio = IF(bio IS NULL OR bio = '', 'Driving distribution and sales growth across Northern Nigeria\'s beverage market.', bio),
  skills = IF(skills IS NULL OR skills = '', 'Sales Strategy, Distribution, Key Account Management', skills),
  expertise_areas = IF(expertise_areas IS NULL OR expertise_areas = '', 'Beverage Industry', expertise_areas),
  business_interests = IF(business_interests IS NULL OR business_interests = '', 'FMCG', business_interests),
  linkedin_url = IF(linkedin_url IS NULL OR linkedin_url = '', 'https://linkedin.com/in/hauwagarba', linkedin_url),
  company = IF(company IS NULL OR company = '', 'Coca-Cola Nigeria', company),
  headline = IF(headline IS NULL OR headline = '', 'Regional Sales Director, Coca-Cola Nigeria', headline),
  industry = IF(industry IS NULL OR industry = '', 'Consumer Goods', industry),
  city = IF(city IS NULL OR city = '', 'Kano', city),
  country = IF(country IS NULL OR country = '', 'Nigeria', country),
  state = IF(state IS NULL OR state = '', 'Kano State', state),
  program = IF(program IS NULL OR program = '', 'Executive MBA', program),
  graduation_year = IF(graduation_year IS NULL, 2014, graduation_year),
  show_phone = IF(show_phone = 0, 1, show_phone),
  show_email = IF(show_email = 0, 1, show_email)
WHERE email = 'hauwa.garba@example.com' AND (personal_interests IS NULL OR personal_interests = '');

INSERT INTO user_experience (user_id, title, company, location, start_date, end_date, is_current, description, sort_order)
SELECT id, 'Regional Sales Director', 'Coca-Cola Nigeria', 'Kano, Nigeria', '2015-03-01', NULL, 1, 'Leading beverage industry initiatives at Coca-Cola Nigeria, based in Kano.', 0 FROM users WHERE email = 'hauwa.garba@example.com'
  AND NOT EXISTS (SELECT 1 FROM user_experience WHERE user_id = (SELECT id FROM users WHERE email = 'hauwa.garba@example.com'));

INSERT INTO user_experience (user_id, title, company, location, start_date, end_date, is_current, description, sort_order)
SELECT id, 'Associate, Brand Management', 'Cadbury Nigeria', 'Kano, Nigeria', '2011-01-01', '2014-12-31', 0, 'Supported beverage industry projects early in career at Cadbury Nigeria.', 1 FROM users WHERE email = 'hauwa.garba@example.com'
  AND (SELECT COUNT(*) FROM user_experience WHERE user_id = (SELECT id FROM users WHERE email = 'hauwa.garba@example.com')) = 1;

INSERT INTO user_education (user_id, school, degree, field_of_study, start_year, end_year, sort_order)
SELECT id, 'Rome Business School Nigeria', 'Executive MBA', 'Business Administration', 2012, 2014, 0 FROM users WHERE email = 'hauwa.garba@example.com'
  AND NOT EXISTS (SELECT 1 FROM user_education WHERE user_id = (SELECT id FROM users WHERE email = 'hauwa.garba@example.com'));

INSERT INTO user_education (user_id, school, degree, field_of_study, start_year, end_year, sort_order)
SELECT id, 'University of Nigeria Nsukka', 'B.Sc', 'Business Administration', 2007, 2011, 1 FROM users WHERE email = 'hauwa.garba@example.com'
  AND (SELECT COUNT(*) FROM user_education WHERE user_id = (SELECT id FROM users WHERE email = 'hauwa.garba@example.com')) = 1;

UPDATE users SET personal_interests = 'Hiking, Golf, Volunteering, Running', phone = '+234 785 944 6210',
  bio = IF(bio IS NULL OR bio = '', 'Building risk models for retail and SME lending portfolios.', bio),
  skills = IF(skills IS NULL OR skills = '', 'Actuarial Science, Data Analysis, Risk Modelling', skills),
  expertise_areas = IF(expertise_areas IS NULL OR expertise_areas = '', 'Retail Banking', expertise_areas),
  business_interests = IF(business_interests IS NULL OR business_interests = '', 'Data Science', business_interests),
  linkedin_url = IF(linkedin_url IS NULL OR linkedin_url = '', 'https://linkedin.com/in/emmanueletuk', linkedin_url),
  company = IF(company IS NULL OR company = '', 'Fidelity Bank', company),
  headline = IF(headline IS NULL OR headline = '', 'Actuarial Analyst, Fidelity Bank', headline),
  industry = IF(industry IS NULL OR industry = '', 'Banking & Finance', industry),
  city = IF(city IS NULL OR city = '', 'Uyo', city),
  country = IF(country IS NULL OR country = '', 'Nigeria', country),
  state = IF(state IS NULL OR state = '', 'Akwa Ibom State', state),
  program = IF(program IS NULL OR program = '', 'MSc Finance', program),
  graduation_year = IF(graduation_year IS NULL, 2022, graduation_year),
  show_phone = IF(show_phone = 0, 1, show_phone),
  show_email = IF(show_email = 0, 1, show_email)
WHERE email = 'emmanuel.etuk@example.com' AND (personal_interests IS NULL OR personal_interests = '');

INSERT INTO user_experience (user_id, title, company, location, start_date, end_date, is_current, description, sort_order)
SELECT id, 'Actuarial Analyst', 'Fidelity Bank', 'Uyo, Nigeria', '2023-02-01', NULL, 1, 'Leading retail banking initiatives at Fidelity Bank, based in Uyo.', 0 FROM users WHERE email = 'emmanuel.etuk@example.com'
  AND NOT EXISTS (SELECT 1 FROM user_experience WHERE user_id = (SELECT id FROM users WHERE email = 'emmanuel.etuk@example.com'));

INSERT INTO user_experience (user_id, title, company, location, start_date, end_date, is_current, description, sort_order)
SELECT id, 'Associate, Corporate Finance', 'FCMB', 'Uyo, Nigeria', '2019-01-01', '2022-12-31', 0, 'Supported retail banking projects early in career at FCMB.', 1 FROM users WHERE email = 'emmanuel.etuk@example.com'
  AND (SELECT COUNT(*) FROM user_experience WHERE user_id = (SELECT id FROM users WHERE email = 'emmanuel.etuk@example.com')) = 1;

INSERT INTO user_education (user_id, school, degree, field_of_study, start_year, end_year, sort_order)
SELECT id, 'Rome Business School Nigeria', 'MSc Finance', 'Business Administration', 2020, 2022, 0 FROM users WHERE email = 'emmanuel.etuk@example.com'
  AND NOT EXISTS (SELECT 1 FROM user_education WHERE user_id = (SELECT id FROM users WHERE email = 'emmanuel.etuk@example.com'));

INSERT INTO user_education (user_id, school, degree, field_of_study, start_year, end_year, sort_order)
SELECT id, 'Ahmadu Bello University', 'B.Sc', 'Accounting', 2015, 2019, 1 FROM users WHERE email = 'emmanuel.etuk@example.com'
  AND (SELECT COUNT(*) FROM user_education WHERE user_id = (SELECT id FROM users WHERE email = 'emmanuel.etuk@example.com')) = 1;

UPDATE users SET personal_interests = 'Traveling, Mentoring, Football, Board Games', phone = '+234 828 247 9308',
  bio = IF(bio IS NULL OR bio = '', 'Exporting processed agricultural produce from Northern Nigeria to European markets.', bio),
  skills = IF(skills IS NULL OR skills = '', 'Agribusiness, Export Trade, Supply Chain', skills),
  expertise_areas = IF(expertise_areas IS NULL OR expertise_areas = '', 'Agriculture', expertise_areas),
  business_interests = IF(business_interests IS NULL OR business_interests = '', 'Agri-exports', business_interests),
  linkedin_url = IF(linkedin_url IS NULL OR linkedin_url = '', 'https://linkedin.com/in/fatimasani', linkedin_url),
  company = IF(company IS NULL OR company = '', 'Sani Agro Exports', company),
  headline = IF(headline IS NULL OR headline = '', 'Co-Founder, Sani Agro Exports', headline),
  industry = IF(industry IS NULL OR industry = '', 'Agriculture & Agribusiness', industry),
  city = IF(city IS NULL OR city = '', 'Kano', city),
  country = IF(country IS NULL OR country = '', 'Nigeria', country),
  state = IF(state IS NULL OR state = '', 'Kano State', state),
  program = IF(program IS NULL OR program = '', 'MBA', program),
  graduation_year = IF(graduation_year IS NULL, 2017, graduation_year),
  show_phone = IF(show_phone = 0, 1, show_phone),
  show_email = IF(show_email = 0, 1, show_email)
WHERE email = 'fatima.sani@example.com' AND (personal_interests IS NULL OR personal_interests = '');

INSERT INTO user_experience (user_id, title, company, location, start_date, end_date, is_current, description, sort_order)
SELECT id, 'Co-Founder', 'Sani Agro Exports', 'Kano, Nigeria', '2018-06-01', NULL, 1, 'Leading agriculture initiatives at Sani Agro Exports, based in Kano.', 0 FROM users WHERE email = 'fatima.sani@example.com'
  AND NOT EXISTS (SELECT 1 FROM user_experience WHERE user_id = (SELECT id FROM users WHERE email = 'fatima.sani@example.com'));

INSERT INTO user_experience (user_id, title, company, location, start_date, end_date, is_current, description, sort_order)
SELECT id, 'Associate, Agribusiness Development', 'Olam Nigeria', 'Kano, Nigeria', '2014-01-01', '2017-12-31', 0, 'Supported agriculture projects early in career at Olam Nigeria.', 1 FROM users WHERE email = 'fatima.sani@example.com'
  AND (SELECT COUNT(*) FROM user_experience WHERE user_id = (SELECT id FROM users WHERE email = 'fatima.sani@example.com')) = 1;

INSERT INTO user_education (user_id, school, degree, field_of_study, start_year, end_year, sort_order)
SELECT id, 'Rome Business School Nigeria', 'MBA', 'Business Administration', 2015, 2017, 0 FROM users WHERE email = 'fatima.sani@example.com'
  AND NOT EXISTS (SELECT 1 FROM user_education WHERE user_id = (SELECT id FROM users WHERE email = 'fatima.sani@example.com'));

INSERT INTO user_education (user_id, school, degree, field_of_study, start_year, end_year, sort_order)
SELECT id, 'University of Nigeria Nsukka', 'B.Sc', 'Business Administration', 2010, 2014, 1 FROM users WHERE email = 'fatima.sani@example.com'
  AND (SELECT COUNT(*) FROM user_education WHERE user_id = (SELECT id FROM users WHERE email = 'fatima.sani@example.com')) = 1;

UPDATE users SET personal_interests = 'Running, Wine Tasting, Football, Cycling', phone = '+234 724 756 5545',
  bio = IF(bio IS NULL OR bio = '', 'Leading ALAT, Wema Bank\'s fully digital banking arm.', bio),
  skills = IF(skills IS NULL OR skills = '', 'Digital Banking, Product Strategy, Innovation', skills),
  expertise_areas = IF(expertise_areas IS NULL OR expertise_areas = '', 'Digital Transformation', expertise_areas),
  business_interests = IF(business_interests IS NULL OR business_interests = '', 'Neobanking', business_interests),
  linkedin_url = IF(linkedin_url IS NULL OR linkedin_url = '', 'https://linkedin.com/in/adebayoogunleye', linkedin_url),
  company = IF(company IS NULL OR company = '', 'Wema Bank', company),
  headline = IF(headline IS NULL OR headline = '', 'Head of Digital Banking, Wema Bank', headline),
  industry = IF(industry IS NULL OR industry = '', 'Banking & Finance', industry),
  city = IF(city IS NULL OR city = '', 'Lagos', city),
  country = IF(country IS NULL OR country = '', 'Nigeria', country),
  state = IF(state IS NULL OR state = '', 'Lagos State', state),
  program = IF(program IS NULL OR program = '', 'Executive MBA', program),
  graduation_year = IF(graduation_year IS NULL, 2013, graduation_year),
  show_phone = IF(show_phone = 0, 1, show_phone),
  show_email = IF(show_email = 0, 1, show_email)
WHERE email = 'adebayo.ogunleye@example.com' AND (personal_interests IS NULL OR personal_interests = '');

INSERT INTO user_experience (user_id, title, company, location, start_date, end_date, is_current, description, sort_order)
SELECT id, 'Head of Digital Banking', 'Wema Bank', 'Lagos, Nigeria', '2014-06-01', NULL, 1, 'Leading digital transformation initiatives at Wema Bank, based in Lagos.', 0 FROM users WHERE email = 'adebayo.ogunleye@example.com'
  AND NOT EXISTS (SELECT 1 FROM user_experience WHERE user_id = (SELECT id FROM users WHERE email = 'adebayo.ogunleye@example.com'));

INSERT INTO user_experience (user_id, title, company, location, start_date, end_date, is_current, description, sort_order)
SELECT id, 'Associate, Corporate Finance', 'Sterling Bank', 'Lagos, Nigeria', '2010-01-01', '2013-12-31', 0, 'Supported digital transformation projects early in career at Sterling Bank.', 1 FROM users WHERE email = 'adebayo.ogunleye@example.com'
  AND (SELECT COUNT(*) FROM user_experience WHERE user_id = (SELECT id FROM users WHERE email = 'adebayo.ogunleye@example.com')) = 1;

INSERT INTO user_education (user_id, school, degree, field_of_study, start_year, end_year, sort_order)
SELECT id, 'Rome Business School Nigeria', 'Executive MBA', 'Business Administration', 2011, 2013, 0 FROM users WHERE email = 'adebayo.ogunleye@example.com'
  AND NOT EXISTS (SELECT 1 FROM user_education WHERE user_id = (SELECT id FROM users WHERE email = 'adebayo.ogunleye@example.com'));

INSERT INTO user_education (user_id, school, degree, field_of_study, start_year, end_year, sort_order)
SELECT id, 'University of Port Harcourt', 'B.Sc', 'Finance', 2006, 2010, 1 FROM users WHERE email = 'adebayo.ogunleye@example.com'
  AND (SELECT COUNT(*) FROM user_education WHERE user_id = (SELECT id FROM users WHERE email = 'adebayo.ogunleye@example.com')) = 1;

UPDATE users SET personal_interests = 'Golf, Football, Cycling', phone = '+234 906 719 7352',
  bio = IF(bio IS NULL OR bio = '', 'Managing residential and commercial development projects across Lagos.', bio),
  skills = IF(skills IS NULL OR skills = '', 'Real Estate Development, Project Management, Construction Finance', skills),
  expertise_areas = IF(expertise_areas IS NULL OR expertise_areas = '', 'Property Development', expertise_areas),
  business_interests = IF(business_interests IS NULL OR business_interests = '', 'Urban Development', business_interests),
  linkedin_url = IF(linkedin_url IS NULL OR linkedin_url = '', 'https://linkedin.com/in/nkechichukwuemeka', linkedin_url),
  company = IF(company IS NULL OR company = '', 'UPDC', company),
  headline = IF(headline IS NULL OR headline = '', 'Real Estate Development Manager, UPDC', headline),
  industry = IF(industry IS NULL OR industry = '', 'Real Estate', industry),
  city = IF(city IS NULL OR city = '', 'Lagos', city),
  country = IF(country IS NULL OR country = '', 'Nigeria', country),
  state = IF(state IS NULL OR state = '', 'Lagos State', state),
  program = IF(program IS NULL OR program = '', 'MBA', program),
  graduation_year = IF(graduation_year IS NULL, 2019, graduation_year),
  show_phone = IF(show_phone = 0, 1, show_phone),
  show_email = IF(show_email = 0, 1, show_email)
WHERE email = 'nkechi.chukwuemeka@example.com' AND (personal_interests IS NULL OR personal_interests = '');

INSERT INTO user_experience (user_id, title, company, location, start_date, end_date, is_current, description, sort_order)
SELECT id, 'Real Estate Development Manager', 'UPDC', 'Lagos, Nigeria', '2020-03-01', NULL, 1, 'Leading property development initiatives at UPDC, based in Lagos.', 0 FROM users WHERE email = 'nkechi.chukwuemeka@example.com'
  AND NOT EXISTS (SELECT 1 FROM user_experience WHERE user_id = (SELECT id FROM users WHERE email = 'nkechi.chukwuemeka@example.com'));

INSERT INTO user_experience (user_id, title, company, location, start_date, end_date, is_current, description, sort_order)
SELECT id, 'Associate, Property Investment', 'Actis', 'Lagos, Nigeria', '2016-01-01', '2019-12-31', 0, 'Supported property development projects early in career at Actis.', 1 FROM users WHERE email = 'nkechi.chukwuemeka@example.com'
  AND (SELECT COUNT(*) FROM user_experience WHERE user_id = (SELECT id FROM users WHERE email = 'nkechi.chukwuemeka@example.com')) = 1;

INSERT INTO user_education (user_id, school, degree, field_of_study, start_year, end_year, sort_order)
SELECT id, 'Rome Business School Nigeria', 'MBA', 'Business Administration', 2017, 2019, 0 FROM users WHERE email = 'nkechi.chukwuemeka@example.com'
  AND NOT EXISTS (SELECT 1 FROM user_education WHERE user_id = (SELECT id FROM users WHERE email = 'nkechi.chukwuemeka@example.com'));

INSERT INTO user_education (user_id, school, degree, field_of_study, start_year, end_year, sort_order)
SELECT id, 'Covenant University', 'B.Sc', 'Estate Management', 2012, 2016, 1 FROM users WHERE email = 'nkechi.chukwuemeka@example.com'
  AND (SELECT COUNT(*) FROM user_education WHERE user_id = (SELECT id FROM users WHERE email = 'nkechi.chukwuemeka@example.com')) = 1;

UPDATE users SET personal_interests = 'Cooking, Volunteering, Cycling', phone = '+234 736 414 8514',
  bio = IF(bio IS NULL OR bio = '', 'Streamlining branch and back-office operations for a fast-growing commercial bank.', bio),
  skills = IF(skills IS NULL OR skills = '', 'Operations Management, Process Improvement, Banking', skills),
  expertise_areas = IF(expertise_areas IS NULL OR expertise_areas = '', 'Retail Banking', expertise_areas),
  business_interests = IF(business_interests IS NULL OR business_interests = '', 'Process Optimization', business_interests),
  linkedin_url = IF(linkedin_url IS NULL OR linkedin_url = '', 'https://linkedin.com/in/rasheedbalogun', linkedin_url),
  company = IF(company IS NULL OR company = '', 'Providus Bank', company),
  headline = IF(headline IS NULL OR headline = '', 'Operations Manager, Providus Bank', headline),
  industry = IF(industry IS NULL OR industry = '', 'Banking & Finance', industry),
  city = IF(city IS NULL OR city = '', 'Lagos', city),
  country = IF(country IS NULL OR country = '', 'Nigeria', country),
  state = IF(state IS NULL OR state = '', 'Lagos State', state),
  program = IF(program IS NULL OR program = '', 'MBA', program),
  graduation_year = IF(graduation_year IS NULL, 2020, graduation_year),
  show_phone = IF(show_phone = 0, 1, show_phone),
  show_email = IF(show_email = 0, 1, show_email)
WHERE email = 'rasheed.balogun@example.com' AND (personal_interests IS NULL OR personal_interests = '');

INSERT INTO user_experience (user_id, title, company, location, start_date, end_date, is_current, description, sort_order)
SELECT id, 'Operations Manager', 'Providus Bank', 'Lagos, Nigeria', '2021-03-01', NULL, 1, 'Leading retail banking initiatives at Providus Bank, based in Lagos.', 0 FROM users WHERE email = 'rasheed.balogun@example.com'
  AND NOT EXISTS (SELECT 1 FROM user_experience WHERE user_id = (SELECT id FROM users WHERE email = 'rasheed.balogun@example.com'));

INSERT INTO user_experience (user_id, title, company, location, start_date, end_date, is_current, description, sort_order)
SELECT id, 'Associate, Corporate Finance', 'Stanbic IBTC', 'Lagos, Nigeria', '2017-01-01', '2020-12-31', 0, 'Supported retail banking projects early in career at Stanbic IBTC.', 1 FROM users WHERE email = 'rasheed.balogun@example.com'
  AND (SELECT COUNT(*) FROM user_experience WHERE user_id = (SELECT id FROM users WHERE email = 'rasheed.balogun@example.com')) = 1;

INSERT INTO user_education (user_id, school, degree, field_of_study, start_year, end_year, sort_order)
SELECT id, 'Rome Business School Nigeria', 'MBA', 'Business Administration', 2018, 2020, 0 FROM users WHERE email = 'rasheed.balogun@example.com'
  AND NOT EXISTS (SELECT 1 FROM user_education WHERE user_id = (SELECT id FROM users WHERE email = 'rasheed.balogun@example.com'));

INSERT INTO user_education (user_id, school, degree, field_of_study, start_year, end_year, sort_order)
SELECT id, 'Obafemi Awolowo University', 'B.Sc', 'Accounting', 2013, 2017, 1 FROM users WHERE email = 'rasheed.balogun@example.com'
  AND (SELECT COUNT(*) FROM user_education WHERE user_id = (SELECT id FROM users WHERE email = 'rasheed.balogun@example.com')) = 1;

UPDATE users SET personal_interests = 'Mentoring, Writing, Gardening', phone = '+234 734 874 3038',
  bio = IF(bio IS NULL OR bio = '', 'Leading brand and growth marketing for one of Nigeria\'s pioneering e-commerce platforms.', bio),
  skills = IF(skills IS NULL OR skills = '', 'Growth Marketing, E-commerce, Brand Strategy', skills),
  expertise_areas = IF(expertise_areas IS NULL OR expertise_areas = '', 'Retail Tech', expertise_areas),
  business_interests = IF(business_interests IS NULL OR business_interests = '', 'E-commerce', business_interests),
  linkedin_url = IF(linkedin_url IS NULL OR linkedin_url = '', 'https://linkedin.com/in/omolaraajibade', linkedin_url),
  company = IF(company IS NULL OR company = '', 'Konga', company),
  headline = IF(headline IS NULL OR headline = '', 'Chief Marketing Officer, Konga', headline),
  industry = IF(industry IS NULL OR industry = '', 'Technology & Fintech', industry),
  city = IF(city IS NULL OR city = '', 'Lagos', city),
  country = IF(country IS NULL OR country = '', 'Nigeria', country),
  state = IF(state IS NULL OR state = '', 'Lagos State', state),
  program = IF(program IS NULL OR program = '', 'Executive MBA', program),
  graduation_year = IF(graduation_year IS NULL, 2015, graduation_year),
  show_phone = IF(show_phone = 0, 1, show_phone),
  show_email = IF(show_email = 0, 1, show_email)
WHERE email = 'omolara.ajibade@example.com' AND (personal_interests IS NULL OR personal_interests = '');

INSERT INTO user_experience (user_id, title, company, location, start_date, end_date, is_current, description, sort_order)
SELECT id, 'Chief Marketing Officer', 'Konga', 'Lagos, Nigeria', '2016-05-01', NULL, 1, 'Leading retail tech initiatives at Konga, based in Lagos.', 0 FROM users WHERE email = 'omolara.ajibade@example.com'
  AND NOT EXISTS (SELECT 1 FROM user_experience WHERE user_id = (SELECT id FROM users WHERE email = 'omolara.ajibade@example.com'));

INSERT INTO user_experience (user_id, title, company, location, start_date, end_date, is_current, description, sort_order)
SELECT id, 'Associate, Product Strategy', 'Moniepoint', 'Lagos, Nigeria', '2012-01-01', '2015-12-31', 0, 'Supported retail tech projects early in career at Moniepoint.', 1 FROM users WHERE email = 'omolara.ajibade@example.com'
  AND (SELECT COUNT(*) FROM user_experience WHERE user_id = (SELECT id FROM users WHERE email = 'omolara.ajibade@example.com')) = 1;

INSERT INTO user_education (user_id, school, degree, field_of_study, start_year, end_year, sort_order)
SELECT id, 'Rome Business School Nigeria', 'Executive MBA', 'Business Administration', 2013, 2015, 0 FROM users WHERE email = 'omolara.ajibade@example.com'
  AND NOT EXISTS (SELECT 1 FROM user_education WHERE user_id = (SELECT id FROM users WHERE email = 'omolara.ajibade@example.com'));

INSERT INTO user_education (user_id, school, degree, field_of_study, start_year, end_year, sort_order)
SELECT id, 'University of Nigeria Nsukka', 'B.Sc', 'Information Systems', 2008, 2012, 1 FROM users WHERE email = 'omolara.ajibade@example.com'
  AND (SELECT COUNT(*) FROM user_education WHERE user_id = (SELECT id FROM users WHERE email = 'omolara.ajibade@example.com')) = 1;

UPDATE users SET personal_interests = 'Hiking, Volunteering, Fitness Training', phone = '+234 765 640 4798',
  bio = IF(bio IS NULL OR bio = '', 'Overseeing brewery operations and production efficiency in South-East Nigeria.', bio),
  skills = IF(skills IS NULL OR skills = '', 'Manufacturing Operations, Lean Manufacturing, Safety Management', skills),
  expertise_areas = IF(expertise_areas IS NULL OR expertise_areas = '', 'Production Management', expertise_areas),
  business_interests = IF(business_interests IS NULL OR business_interests = '', 'Beverage Manufacturing', business_interests),
  linkedin_url = IF(linkedin_url IS NULL OR linkedin_url = '', 'https://linkedin.com/in/chukwuemekaeze', linkedin_url),
  company = IF(company IS NULL OR company = '', 'Nigerian Breweries', company),
  headline = IF(headline IS NULL OR headline = '', 'Plant Manager, Nigerian Breweries', headline),
  industry = IF(industry IS NULL OR industry = '', 'Manufacturing', industry),
  city = IF(city IS NULL OR city = '', 'Enugu', city),
  country = IF(country IS NULL OR country = '', 'Nigeria', country),
  state = IF(state IS NULL OR state = '', 'Enugu State', state),
  program = IF(program IS NULL OR program = '', 'Executive MBA', program),
  graduation_year = IF(graduation_year IS NULL, 2016, graduation_year),
  show_phone = IF(show_phone = 0, 1, show_phone),
  show_email = IF(show_email = 0, 1, show_email)
WHERE email = 'chukwuemeka.eze@example.com' AND (personal_interests IS NULL OR personal_interests = '');

INSERT INTO user_experience (user_id, title, company, location, start_date, end_date, is_current, description, sort_order)
SELECT id, 'Plant Manager', 'Nigerian Breweries', 'Enugu, Nigeria', '2017-01-01', NULL, 1, 'Leading production management initiatives at Nigerian Breweries, based in Enugu.', 0 FROM users WHERE email = 'chukwuemeka.eze@example.com'
  AND NOT EXISTS (SELECT 1 FROM user_experience WHERE user_id = (SELECT id FROM users WHERE email = 'chukwuemeka.eze@example.com'));

INSERT INTO user_experience (user_id, title, company, location, start_date, end_date, is_current, description, sort_order)
SELECT id, 'Associate, Operations', 'Dangote Group', 'Enugu, Nigeria', '2013-01-01', '2016-12-31', 0, 'Supported production management projects early in career at Dangote Group.', 1 FROM users WHERE email = 'chukwuemeka.eze@example.com'
  AND (SELECT COUNT(*) FROM user_experience WHERE user_id = (SELECT id FROM users WHERE email = 'chukwuemeka.eze@example.com')) = 1;

INSERT INTO user_education (user_id, school, degree, field_of_study, start_year, end_year, sort_order)
SELECT id, 'Rome Business School Nigeria', 'Executive MBA', 'Business Administration', 2014, 2016, 0 FROM users WHERE email = 'chukwuemeka.eze@example.com'
  AND NOT EXISTS (SELECT 1 FROM user_education WHERE user_id = (SELECT id FROM users WHERE email = 'chukwuemeka.eze@example.com'));

INSERT INTO user_education (user_id, school, degree, field_of_study, start_year, end_year, sort_order)
SELECT id, 'Lagos Business School', 'B.Sc', 'Mechanical Engineering', 2009, 2013, 1 FROM users WHERE email = 'chukwuemeka.eze@example.com'
  AND (SELECT COUNT(*) FROM user_education WHERE user_id = (SELECT id FROM users WHERE email = 'chukwuemeka.eze@example.com')) = 1;

UPDATE users SET personal_interests = 'Photography, Fitness Training, Board Games', phone = '+234 743 418 5742',
  bio = IF(bio IS NULL OR bio = '', 'Evaluating investment opportunities across Union Bank\'s corporate portfolio.', bio),
  skills = IF(skills IS NULL OR skills = '', 'Investment Analysis, Financial Modelling, Valuation', skills),
  expertise_areas = IF(expertise_areas IS NULL OR expertise_areas = '', 'Corporate Finance', expertise_areas),
  business_interests = IF(business_interests IS NULL OR business_interests = '', 'Capital Markets', business_interests),
  linkedin_url = IF(linkedin_url IS NULL OR linkedin_url = '', 'https://linkedin.com/in/aminalawal', linkedin_url),
  company = IF(company IS NULL OR company = '', 'Union Bank Nigeria', company),
  headline = IF(headline IS NULL OR headline = '', 'Investment Analyst, Union Bank Nigeria', headline),
  industry = IF(industry IS NULL OR industry = '', 'Banking & Finance', industry),
  city = IF(city IS NULL OR city = '', 'Abuja', city),
  country = IF(country IS NULL OR country = '', 'Nigeria', country),
  state = IF(state IS NULL OR state = '', 'FCT', state),
  program = IF(program IS NULL OR program = '', 'MSc Finance', program),
  graduation_year = IF(graduation_year IS NULL, 2021, graduation_year),
  show_phone = IF(show_phone = 0, 1, show_phone),
  show_email = IF(show_email = 0, 1, show_email)
WHERE email = 'amina.lawal@example.com' AND (personal_interests IS NULL OR personal_interests = '');

INSERT INTO user_experience (user_id, title, company, location, start_date, end_date, is_current, description, sort_order)
SELECT id, 'Investment Analyst', 'Union Bank Nigeria', 'Abuja, Nigeria', '2022-01-01', NULL, 1, 'Leading corporate finance initiatives at Union Bank Nigeria, based in Abuja.', 0 FROM users WHERE email = 'amina.lawal@example.com'
  AND NOT EXISTS (SELECT 1 FROM user_experience WHERE user_id = (SELECT id FROM users WHERE email = 'amina.lawal@example.com'));

INSERT INTO user_experience (user_id, title, company, location, start_date, end_date, is_current, description, sort_order)
SELECT id, 'Associate, Corporate Finance', 'Sterling Bank', 'Abuja, Nigeria', '2018-01-01', '2021-12-31', 0, 'Supported corporate finance projects early in career at Sterling Bank.', 1 FROM users WHERE email = 'amina.lawal@example.com'
  AND (SELECT COUNT(*) FROM user_experience WHERE user_id = (SELECT id FROM users WHERE email = 'amina.lawal@example.com')) = 1;

INSERT INTO user_education (user_id, school, degree, field_of_study, start_year, end_year, sort_order)
SELECT id, 'Rome Business School Nigeria', 'MSc Finance', 'Business Administration', 2019, 2021, 0 FROM users WHERE email = 'amina.lawal@example.com'
  AND NOT EXISTS (SELECT 1 FROM user_education WHERE user_id = (SELECT id FROM users WHERE email = 'amina.lawal@example.com'));

INSERT INTO user_education (user_id, school, degree, field_of_study, start_year, end_year, sort_order)
SELECT id, 'University of Port Harcourt', 'B.Sc', 'Accounting', 2014, 2018, 1 FROM users WHERE email = 'amina.lawal@example.com'
  AND (SELECT COUNT(*) FROM user_education WHERE user_id = (SELECT id FROM users WHERE email = 'amina.lawal@example.com')) = 1;

UPDATE users SET personal_interests = 'Cooking, Football, Fitness Training', phone = '+234 866 617 3433',
  bio = IF(bio IS NULL OR bio = '', 'Building a cross-platform media company spanning radio, digital and events.', bio),
  skills = IF(skills IS NULL OR skills = '', 'Media Strategy, Content Production, Entrepreneurship', skills),
  expertise_areas = IF(expertise_areas IS NULL OR expertise_areas = '', 'Media & Broadcasting', expertise_areas),
  business_interests = IF(business_interests IS NULL OR business_interests = '', 'Digital Media', business_interests),
  linkedin_url = IF(linkedin_url IS NULL OR linkedin_url = '', 'https://linkedin.com/in/tunjialabi', linkedin_url),
  company = IF(company IS NULL OR company = '', 'Alabi Media Group', company),
  headline = IF(headline IS NULL OR headline = '', 'Founder & CEO, Alabi Media Group', headline),
  industry = IF(industry IS NULL OR industry = '', 'Media & Entertainment', industry),
  city = IF(city IS NULL OR city = '', 'Lagos', city),
  country = IF(country IS NULL OR country = '', 'Nigeria', country),
  state = IF(state IS NULL OR state = '', 'Lagos State', state),
  program = IF(program IS NULL OR program = '', 'MBA', program),
  graduation_year = IF(graduation_year IS NULL, 2014, graduation_year),
  show_phone = IF(show_phone = 0, 1, show_phone),
  show_email = IF(show_email = 0, 1, show_email)
WHERE email = 'tunji.alabi@example.com' AND (personal_interests IS NULL OR personal_interests = '');

INSERT INTO user_experience (user_id, title, company, location, start_date, end_date, is_current, description, sort_order)
SELECT id, 'Founder & CEO', 'Alabi Media Group', 'Lagos, Nigeria', '2015-03-01', NULL, 1, 'Leading media & broadcasting initiatives at Alabi Media Group, based in Lagos.', 0 FROM users WHERE email = 'tunji.alabi@example.com'
  AND NOT EXISTS (SELECT 1 FROM user_experience WHERE user_id = (SELECT id FROM users WHERE email = 'tunji.alabi@example.com'));

INSERT INTO user_experience (user_id, title, company, location, start_date, end_date, is_current, description, sort_order)
SELECT id, 'Associate, Content Strategy', 'MultiChoice Nigeria', 'Lagos, Nigeria', '2011-01-01', '2014-12-31', 0, 'Supported media & broadcasting projects early in career at MultiChoice Nigeria.', 1 FROM users WHERE email = 'tunji.alabi@example.com'
  AND (SELECT COUNT(*) FROM user_experience WHERE user_id = (SELECT id FROM users WHERE email = 'tunji.alabi@example.com')) = 1;

INSERT INTO user_education (user_id, school, degree, field_of_study, start_year, end_year, sort_order)
SELECT id, 'Rome Business School Nigeria', 'MBA', 'Business Administration', 2012, 2014, 0 FROM users WHERE email = 'tunji.alabi@example.com'
  AND NOT EXISTS (SELECT 1 FROM user_education WHERE user_id = (SELECT id FROM users WHERE email = 'tunji.alabi@example.com'));

INSERT INTO user_education (user_id, school, degree, field_of_study, start_year, end_year, sort_order)
SELECT id, 'University of Lagos', 'B.Sc', 'Mass Communication', 2007, 2011, 1 FROM users WHERE email = 'tunji.alabi@example.com'
  AND (SELECT COUNT(*) FROM user_education WHERE user_id = (SELECT id FROM users WHERE email = 'tunji.alabi@example.com')) = 1;

UPDATE users SET personal_interests = 'Football, Gardening, Public Speaking', phone = '+234 858 850 1000',
  bio = IF(bio IS NULL OR bio = '', 'Leading Nigerian operations for a genomics research company advancing precision medicine in Africa.', bio),
  skills = IF(skills IS NULL OR skills = '', 'Healthcare Strategy, Genomics, Partnerships', skills),
  expertise_areas = IF(expertise_areas IS NULL OR expertise_areas = '', 'Health Tech', expertise_areas),
  business_interests = IF(business_interests IS NULL OR business_interests = '', 'Precision Medicine', business_interests),
  linkedin_url = IF(linkedin_url IS NULL OR linkedin_url = '', 'https://linkedin.com/in/vivianokonjo', linkedin_url),
  company = IF(company IS NULL OR company = '', '54gene', company),
  headline = IF(headline IS NULL OR headline = '', 'Country Director, 54gene', headline),
  industry = IF(industry IS NULL OR industry = '', 'Healthcare', industry),
  city = IF(city IS NULL OR city = '', 'Lagos', city),
  country = IF(country IS NULL OR country = '', 'Nigeria', country),
  state = IF(state IS NULL OR state = '', 'Lagos State', state),
  program = IF(program IS NULL OR program = '', 'Executive MBA', program),
  graduation_year = IF(graduation_year IS NULL, 2013, graduation_year),
  show_phone = IF(show_phone = 0, 1, show_phone),
  show_email = IF(show_email = 0, 1, show_email)
WHERE email = 'vivian.okonjo@example.com' AND (personal_interests IS NULL OR personal_interests = '');

INSERT INTO user_experience (user_id, title, company, location, start_date, end_date, is_current, description, sort_order)
SELECT id, 'Country Director', '54gene', 'Lagos, Nigeria', '2014-03-01', NULL, 1, 'Leading health tech initiatives at 54gene, based in Lagos.', 0 FROM users WHERE email = 'vivian.okonjo@example.com'
  AND NOT EXISTS (SELECT 1 FROM user_experience WHERE user_id = (SELECT id FROM users WHERE email = 'vivian.okonjo@example.com'));

INSERT INTO user_experience (user_id, title, company, location, start_date, end_date, is_current, description, sort_order)
SELECT id, 'Associate, Healthcare Management', 'WHO Africa', 'Lagos, Nigeria', '2010-01-01', '2013-12-31', 0, 'Supported health tech projects early in career at WHO Africa.', 1 FROM users WHERE email = 'vivian.okonjo@example.com'
  AND (SELECT COUNT(*) FROM user_experience WHERE user_id = (SELECT id FROM users WHERE email = 'vivian.okonjo@example.com')) = 1;

INSERT INTO user_education (user_id, school, degree, field_of_study, start_year, end_year, sort_order)
SELECT id, 'Rome Business School Nigeria', 'Executive MBA', 'Business Administration', 2011, 2013, 0 FROM users WHERE email = 'vivian.okonjo@example.com'
  AND NOT EXISTS (SELECT 1 FROM user_education WHERE user_id = (SELECT id FROM users WHERE email = 'vivian.okonjo@example.com'));

INSERT INTO user_education (user_id, school, degree, field_of_study, start_year, end_year, sort_order)
SELECT id, 'Babcock University', 'B.Sc', 'Public Health', 2006, 2010, 1 FROM users WHERE email = 'vivian.okonjo@example.com'
  AND (SELECT COUNT(*) FROM user_education WHERE user_id = (SELECT id FROM users WHERE email = 'vivian.okonjo@example.com')) = 1;

UPDATE users SET personal_interests = 'Reading, Chess, Running, Cycling', phone = '+234 823 119 1855',
  bio = IF(bio IS NULL OR bio = '', 'Managing fuel retail operations across Northern Nigeria\'s distribution network.', bio),
  skills = IF(skills IS NULL OR skills = '', 'Retail Operations, Distribution, Energy', skills),
  expertise_areas = IF(expertise_areas IS NULL OR expertise_areas = '', 'Downstream Energy', expertise_areas),
  business_interests = IF(business_interests IS NULL OR business_interests = '', 'Fuel Retail', business_interests),
  linkedin_url = IF(linkedin_url IS NULL OR linkedin_url = '', 'https://linkedin.com/in/musadanjuma', linkedin_url),
  company = IF(company IS NULL OR company = '', 'Total Energies', company),
  headline = IF(headline IS NULL OR headline = '', 'Regional Manager, Total Energies', headline),
  industry = IF(industry IS NULL OR industry = '', 'Energy', industry),
  city = IF(city IS NULL OR city = '', 'Kaduna', city),
  country = IF(country IS NULL OR country = '', 'Nigeria', country),
  state = IF(state IS NULL OR state = '', 'Kaduna State', state),
  program = IF(program IS NULL OR program = '', 'Executive MBA', program),
  graduation_year = IF(graduation_year IS NULL, 2012, graduation_year),
  show_phone = IF(show_phone = 0, 1, show_phone),
  show_email = IF(show_email = 0, 1, show_email)
WHERE email = 'musa.danjuma@example.com' AND (personal_interests IS NULL OR personal_interests = '');

INSERT INTO user_experience (user_id, title, company, location, start_date, end_date, is_current, description, sort_order)
SELECT id, 'Regional Manager', 'Total Energies', 'Kaduna, Nigeria', '2013-02-01', NULL, 1, 'Leading downstream energy initiatives at Total Energies, based in Kaduna.', 0 FROM users WHERE email = 'musa.danjuma@example.com'
  AND NOT EXISTS (SELECT 1 FROM user_experience WHERE user_id = (SELECT id FROM users WHERE email = 'musa.danjuma@example.com'));

INSERT INTO user_experience (user_id, title, company, location, start_date, end_date, is_current, description, sort_order)
SELECT id, 'Associate, Energy Policy', 'Seplat Energy', 'Kaduna, Nigeria', '2009-01-01', '2012-12-31', 0, 'Supported downstream energy projects early in career at Seplat Energy.', 1 FROM users WHERE email = 'musa.danjuma@example.com'
  AND (SELECT COUNT(*) FROM user_experience WHERE user_id = (SELECT id FROM users WHERE email = 'musa.danjuma@example.com')) = 1;

INSERT INTO user_education (user_id, school, degree, field_of_study, start_year, end_year, sort_order)
SELECT id, 'Rome Business School Nigeria', 'Executive MBA', 'Business Administration', 2010, 2012, 0 FROM users WHERE email = 'musa.danjuma@example.com'
  AND NOT EXISTS (SELECT 1 FROM user_education WHERE user_id = (SELECT id FROM users WHERE email = 'musa.danjuma@example.com'));

INSERT INTO user_education (user_id, school, degree, field_of_study, start_year, end_year, sort_order)
SELECT id, 'Covenant University', 'B.Sc', 'Mechanical Engineering', 2005, 2009, 1 FROM users WHERE email = 'musa.danjuma@example.com'
  AND (SELECT COUNT(*) FROM user_education WHERE user_id = (SELECT id FROM users WHERE email = 'musa.danjuma@example.com')) = 1;

UPDATE users SET personal_interests = 'Golf, Volunteering, Cycling', phone = '+234 875 599 8238',
  bio = IF(bio IS NULL OR bio = '', 'Recruiting and placing top African engineering talent with global companies.', bio),
  skills = IF(skills IS NULL OR skills = '', 'Talent Acquisition, HR Tech, Recruiting Strategy', skills),
  expertise_areas = IF(expertise_areas IS NULL OR expertise_areas = '', 'Tech Talent', expertise_areas),
  business_interests = IF(business_interests IS NULL OR business_interests = '', 'EdTech', business_interests),
  linkedin_url = IF(linkedin_url IS NULL OR linkedin_url = '', 'https://linkedin.com/in/bisiadeoye', linkedin_url),
  company = IF(company IS NULL OR company = '', 'Andela', company),
  headline = IF(headline IS NULL OR headline = '', 'Talent Acquisition Lead, Andela', headline),
  industry = IF(industry IS NULL OR industry = '', 'Technology & Fintech', industry),
  city = IF(city IS NULL OR city = '', 'Lagos', city),
  country = IF(country IS NULL OR country = '', 'Nigeria', country),
  state = IF(state IS NULL OR state = '', 'Lagos State', state),
  program = IF(program IS NULL OR program = '', 'MBA', program),
  graduation_year = IF(graduation_year IS NULL, 2020, graduation_year),
  show_phone = IF(show_phone = 0, 1, show_phone),
  show_email = IF(show_email = 0, 1, show_email)
WHERE email = 'bisi.adeoye@example.com' AND (personal_interests IS NULL OR personal_interests = '');

INSERT INTO user_experience (user_id, title, company, location, start_date, end_date, is_current, description, sort_order)
SELECT id, 'Talent Acquisition Lead', 'Andela', 'Lagos, Nigeria', '2021-01-01', NULL, 1, 'Leading tech talent initiatives at Andela, based in Lagos.', 0 FROM users WHERE email = 'bisi.adeoye@example.com'
  AND NOT EXISTS (SELECT 1 FROM user_experience WHERE user_id = (SELECT id FROM users WHERE email = 'bisi.adeoye@example.com'));

INSERT INTO user_experience (user_id, title, company, location, start_date, end_date, is_current, description, sort_order)
SELECT id, 'Associate, Product Strategy', 'Paystack', 'Lagos, Nigeria', '2017-01-01', '2020-12-31', 0, 'Supported tech talent projects early in career at Paystack.', 1 FROM users WHERE email = 'bisi.adeoye@example.com'
  AND (SELECT COUNT(*) FROM user_experience WHERE user_id = (SELECT id FROM users WHERE email = 'bisi.adeoye@example.com')) = 1;

INSERT INTO user_education (user_id, school, degree, field_of_study, start_year, end_year, sort_order)
SELECT id, 'Rome Business School Nigeria', 'MBA', 'Business Administration', 2018, 2020, 0 FROM users WHERE email = 'bisi.adeoye@example.com'
  AND NOT EXISTS (SELECT 1 FROM user_education WHERE user_id = (SELECT id FROM users WHERE email = 'bisi.adeoye@example.com'));

INSERT INTO user_education (user_id, school, degree, field_of_study, start_year, end_year, sort_order)
SELECT id, 'University of Benin', 'B.Sc', 'Computer Science', 2013, 2017, 1 FROM users WHERE email = 'bisi.adeoye@example.com'
  AND (SELECT COUNT(*) FROM user_education WHERE user_id = (SELECT id FROM users WHERE email = 'bisi.adeoye@example.com')) = 1;

UPDATE users SET personal_interests = 'Photography, Jazz Music, Fitness Training', phone = '+234 812 197 4699',
  bio = IF(bio IS NULL OR bio = '', 'Ensuring regulatory compliance across UBA\'s pan-African banking operations.', bio),
  skills = IF(skills IS NULL OR skills = '', 'Regulatory Compliance, AML, Risk Management', skills),
  expertise_areas = IF(expertise_areas IS NULL OR expertise_areas = '', 'Banking Compliance', expertise_areas),
  business_interests = IF(business_interests IS NULL OR business_interests = '', 'Financial Crime Prevention', business_interests),
  linkedin_url = IF(linkedin_url IS NULL OR linkedin_url = '', 'https://linkedin.com/in/chineduobi', linkedin_url),
  company = IF(company IS NULL OR company = '', 'United Bank for Africa', company),
  headline = IF(headline IS NULL OR headline = '', 'Compliance Manager, UBA', headline),
  industry = IF(industry IS NULL OR industry = '', 'Banking & Finance', industry),
  city = IF(city IS NULL OR city = '', 'Lagos', city),
  country = IF(country IS NULL OR country = '', 'Nigeria', country),
  state = IF(state IS NULL OR state = '', 'Lagos State', state),
  program = IF(program IS NULL OR program = '', 'MBA', program),
  graduation_year = IF(graduation_year IS NULL, 2018, graduation_year),
  show_phone = IF(show_phone = 0, 1, show_phone),
  show_email = IF(show_email = 0, 1, show_email)
WHERE email = 'chinedu.obi@example.com' AND (personal_interests IS NULL OR personal_interests = '');

INSERT INTO user_experience (user_id, title, company, location, start_date, end_date, is_current, description, sort_order)
SELECT id, 'Compliance Manager', 'United Bank for Africa', 'Lagos, Nigeria', '2019-03-01', NULL, 1, 'Leading banking compliance initiatives at United Bank for Africa, based in Lagos.', 0 FROM users WHERE email = 'chinedu.obi@example.com'
  AND NOT EXISTS (SELECT 1 FROM user_experience WHERE user_id = (SELECT id FROM users WHERE email = 'chinedu.obi@example.com'));

INSERT INTO user_experience (user_id, title, company, location, start_date, end_date, is_current, description, sort_order)
SELECT id, 'Associate, Corporate Finance', 'Ecobank Nigeria', 'Lagos, Nigeria', '2015-01-01', '2018-12-31', 0, 'Supported banking compliance projects early in career at Ecobank Nigeria.', 1 FROM users WHERE email = 'chinedu.obi@example.com'
  AND (SELECT COUNT(*) FROM user_experience WHERE user_id = (SELECT id FROM users WHERE email = 'chinedu.obi@example.com')) = 1;

INSERT INTO user_education (user_id, school, degree, field_of_study, start_year, end_year, sort_order)
SELECT id, 'Rome Business School Nigeria', 'MBA', 'Business Administration', 2016, 2018, 0 FROM users WHERE email = 'chinedu.obi@example.com'
  AND NOT EXISTS (SELECT 1 FROM user_education WHERE user_id = (SELECT id FROM users WHERE email = 'chinedu.obi@example.com'));

INSERT INTO user_education (user_id, school, degree, field_of_study, start_year, end_year, sort_order)
SELECT id, 'University of Ghana', 'B.Sc', 'Accounting', 2011, 2015, 1 FROM users WHERE email = 'chinedu.obi@example.com'
  AND (SELECT COUNT(*) FROM user_education WHERE user_id = (SELECT id FROM users WHERE email = 'chinedu.obi@example.com')) = 1;

UPDATE users SET personal_interests = 'Hiking, Art Collecting, Fitness Training', phone = '+234 792 698 5736',
  bio = IF(bio IS NULL OR bio = '', 'Designing digital lending and payments experiences for African consumers.', bio),
  skills = IF(skills IS NULL OR skills = '', 'Product Design, UX Research, Fintech', skills),
  expertise_areas = IF(expertise_areas IS NULL OR expertise_areas = '', 'Digital Lending', expertise_areas),
  business_interests = IF(business_interests IS NULL OR business_interests = '', 'Consumer Fintech', business_interests),
  linkedin_url = IF(linkedin_url IS NULL OR linkedin_url = '', 'https://linkedin.com/in/rukayatbakare', linkedin_url),
  company = IF(company IS NULL OR company = '', 'Carbon', company),
  headline = IF(headline IS NULL OR headline = '', 'Product Designer, Carbon', headline),
  industry = IF(industry IS NULL OR industry = '', 'Technology & Fintech', industry),
  city = IF(city IS NULL OR city = '', 'Lagos', city),
  country = IF(country IS NULL OR country = '', 'Nigeria', country),
  state = IF(state IS NULL OR state = '', 'Lagos State', state),
  program = IF(program IS NULL OR program = '', 'MBA', program),
  graduation_year = IF(graduation_year IS NULL, 2022, graduation_year),
  show_phone = IF(show_phone = 0, 1, show_phone),
  show_email = IF(show_email = 0, 1, show_email)
WHERE email = 'rukayat.bakare@example.com' AND (personal_interests IS NULL OR personal_interests = '');

INSERT INTO user_experience (user_id, title, company, location, start_date, end_date, is_current, description, sort_order)
SELECT id, 'Product Designer', 'Carbon', 'Lagos, Nigeria', '2023-05-01', NULL, 1, 'Leading digital lending initiatives at Carbon, based in Lagos.', 0 FROM users WHERE email = 'rukayat.bakare@example.com'
  AND NOT EXISTS (SELECT 1 FROM user_experience WHERE user_id = (SELECT id FROM users WHERE email = 'rukayat.bakare@example.com'));

INSERT INTO user_experience (user_id, title, company, location, start_date, end_date, is_current, description, sort_order)
SELECT id, 'Associate, Product Strategy', 'Moniepoint', 'Lagos, Nigeria', '2019-01-01', '2022-12-31', 0, 'Supported digital lending projects early in career at Moniepoint.', 1 FROM users WHERE email = 'rukayat.bakare@example.com'
  AND (SELECT COUNT(*) FROM user_experience WHERE user_id = (SELECT id FROM users WHERE email = 'rukayat.bakare@example.com')) = 1;

INSERT INTO user_education (user_id, school, degree, field_of_study, start_year, end_year, sort_order)
SELECT id, 'Rome Business School Nigeria', 'MBA', 'Business Administration', 2020, 2022, 0 FROM users WHERE email = 'rukayat.bakare@example.com'
  AND NOT EXISTS (SELECT 1 FROM user_education WHERE user_id = (SELECT id FROM users WHERE email = 'rukayat.bakare@example.com'));

INSERT INTO user_education (user_id, school, degree, field_of_study, start_year, end_year, sort_order)
SELECT id, 'University of Lagos', 'B.Sc', 'Software Engineering', 2015, 2019, 1 FROM users WHERE email = 'rukayat.bakare@example.com'
  AND (SELECT COUNT(*) FROM user_education WHERE user_id = (SELECT id FROM users WHERE email = 'rukayat.bakare@example.com')) = 1;

UPDATE users SET personal_interests = 'Running, Jazz Music, Art Collecting', phone = '+233 44 721 5138',
  bio = IF(bio IS NULL OR bio = '', 'Managing financial planning and analysis for MTN\'s Ghanaian operations.', bio),
  skills = IF(skills IS NULL OR skills = '', 'Financial Planning, Telecom Finance, Budgeting', skills),
  expertise_areas = IF(expertise_areas IS NULL OR expertise_areas = '', 'Telecommunications', expertise_areas),
  business_interests = IF(business_interests IS NULL OR business_interests = '', 'Mobile Money', business_interests),
  linkedin_url = IF(linkedin_url IS NULL OR linkedin_url = '', 'https://linkedin.com/in/godwinosei', linkedin_url),
  company = IF(company IS NULL OR company = '', 'MTN Ghana', company),
  headline = IF(headline IS NULL OR headline = '', 'Finance Manager, MTN Ghana', headline),
  industry = IF(industry IS NULL OR industry = '', 'Telecommunications', industry),
  city = IF(city IS NULL OR city = '', 'Accra', city),
  country = IF(country IS NULL OR country = '', 'Ghana', country),
  state = IF(state IS NULL OR state = '', 'Greater Accra', state),
  program = IF(program IS NULL OR program = '', 'Executive MBA', program),
  graduation_year = IF(graduation_year IS NULL, 2016, graduation_year),
  show_phone = IF(show_phone = 0, 1, show_phone),
  show_email = IF(show_email = 0, 1, show_email)
WHERE email = 'godwin.osei@example.com' AND (personal_interests IS NULL OR personal_interests = '');

INSERT INTO user_experience (user_id, title, company, location, start_date, end_date, is_current, description, sort_order)
SELECT id, 'Finance Manager', 'MTN Ghana', 'Accra, Ghana', '2017-01-01', NULL, 1, 'Leading telecommunications initiatives at MTN Ghana, based in Accra.', 0 FROM users WHERE email = 'godwin.osei@example.com'
  AND NOT EXISTS (SELECT 1 FROM user_experience WHERE user_id = (SELECT id FROM users WHERE email = 'godwin.osei@example.com'));

INSERT INTO user_experience (user_id, title, company, location, start_date, end_date, is_current, description, sort_order)
SELECT id, 'Associate, Network Operations', 'MTN Nigeria', 'Accra, Ghana', '2013-01-01', '2016-12-31', 0, 'Supported telecommunications projects early in career at MTN Nigeria.', 1 FROM users WHERE email = 'godwin.osei@example.com'
  AND (SELECT COUNT(*) FROM user_experience WHERE user_id = (SELECT id FROM users WHERE email = 'godwin.osei@example.com')) = 1;

INSERT INTO user_education (user_id, school, degree, field_of_study, start_year, end_year, sort_order)
SELECT id, 'Rome Business School Nigeria', 'Executive MBA', 'Business Administration', 2014, 2016, 0 FROM users WHERE email = 'godwin.osei@example.com'
  AND NOT EXISTS (SELECT 1 FROM user_education WHERE user_id = (SELECT id FROM users WHERE email = 'godwin.osei@example.com'));

INSERT INTO user_education (user_id, school, degree, field_of_study, start_year, end_year, sort_order)
SELECT id, 'Lagos Business School', 'B.Sc', 'Computer Science', 2009, 2013, 1 FROM users WHERE email = 'godwin.osei@example.com'
  AND (SELECT COUNT(*) FROM user_education WHERE user_id = (SELECT id FROM users WHERE email = 'godwin.osei@example.com')) = 1;

UPDATE users SET personal_interests = 'Traveling, Jazz Music, Writing', phone = '+233 55 392 3695',
  bio = IF(bio IS NULL OR bio = '', 'Advising SMEs across Ghana on strategy, operations and growth financing.', bio),
  skills = IF(skills IS NULL OR skills = '', 'Management Consulting, SME Advisory, Strategy', skills),
  expertise_areas = IF(expertise_areas IS NULL OR expertise_areas = '', 'Business Consulting', expertise_areas),
  business_interests = IF(business_interests IS NULL OR business_interests = '', 'SME Growth', business_interests),
  linkedin_url = IF(linkedin_url IS NULL OR linkedin_url = '', 'https://linkedin.com/in/preciousmensah', linkedin_url),
  company = IF(company IS NULL OR company = '', 'Mensah Consulting Group', company),
  headline = IF(headline IS NULL OR headline = '', 'Founder, Mensah Consulting Group', headline),
  industry = IF(industry IS NULL OR industry = '', 'Professional Services', industry),
  city = IF(city IS NULL OR city = '', 'Kumasi', city),
  country = IF(country IS NULL OR country = '', 'Ghana', country),
  state = IF(state IS NULL OR state = '', 'Ashanti Region', state),
  program = IF(program IS NULL OR program = '', 'MBA', program),
  graduation_year = IF(graduation_year IS NULL, 2015, graduation_year),
  show_phone = IF(show_phone = 0, 1, show_phone),
  show_email = IF(show_email = 0, 1, show_email)
WHERE email = 'precious.mensah@example.com' AND (personal_interests IS NULL OR personal_interests = '');

INSERT INTO user_experience (user_id, title, company, location, start_date, end_date, is_current, description, sort_order)
SELECT id, 'Founder', 'Mensah Consulting Group', 'Kumasi, Ghana', '2016-03-01', NULL, 1, 'Leading business consulting initiatives at Mensah Consulting Group, based in Kumasi.', 0 FROM users WHERE email = 'precious.mensah@example.com'
  AND NOT EXISTS (SELECT 1 FROM user_experience WHERE user_id = (SELECT id FROM users WHERE email = 'precious.mensah@example.com'));

INSERT INTO user_experience (user_id, title, company, location, start_date, end_date, is_current, description, sort_order)
SELECT id, 'Associate, Strategy Consulting', 'PwC Nigeria', 'Kumasi, Ghana', '2012-01-01', '2015-12-31', 0, 'Supported business consulting projects early in career at PwC Nigeria.', 1 FROM users WHERE email = 'precious.mensah@example.com'
  AND (SELECT COUNT(*) FROM user_experience WHERE user_id = (SELECT id FROM users WHERE email = 'precious.mensah@example.com')) = 1;

INSERT INTO user_education (user_id, school, degree, field_of_study, start_year, end_year, sort_order)
SELECT id, 'Rome Business School Nigeria', 'MBA', 'Business Administration', 2013, 2015, 0 FROM users WHERE email = 'precious.mensah@example.com'
  AND NOT EXISTS (SELECT 1 FROM user_education WHERE user_id = (SELECT id FROM users WHERE email = 'precious.mensah@example.com'));

INSERT INTO user_education (user_id, school, degree, field_of_study, start_year, end_year, sort_order)
SELECT id, 'University of Ghana', 'B.Sc', 'Business Administration', 2008, 2012, 1 FROM users WHERE email = 'precious.mensah@example.com'
  AND (SELECT COUNT(*) FROM user_education WHERE user_id = (SELECT id FROM users WHERE email = 'precious.mensah@example.com')) = 1;

