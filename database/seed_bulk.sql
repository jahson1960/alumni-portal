-- Bulk sample data: 20+ alumni, jobs, news, events, plus a healthy spread of
-- companies, resources, businesses, benefits, campaigns, and feed posts.
-- Safe to re-run: every INSERT is guarded by a NOT EXISTS check on a unique
-- field, so running this twice will not create duplicates.
-- All seeded alumni share the same sample password as the original seed
-- users: ChangeMe@123 (bcrypt hash copied from users.id = 2).
-- Run against an already-selected database (phpMyAdmin) or on the command line
-- as `mysql ... your_db_name < seed_bulk.sql` - no USE/CREATE DATABASE here,
-- since most hosts don't grant the app's DB user that permission.

SET @pw := (SELECT password_hash FROM users WHERE email = 'chidi.okonkwo@example.com' LIMIT 1);

-- ---------------------------------------------------------------------
-- 20 additional alumni
-- ---------------------------------------------------------------------
INSERT INTO users (role, name, email, password_hash, headline, company, industry, city, country, graduation_year, program, bio, skills, expertise_areas, business_interests, linkedin_url, status, profile_visibility, is_mentor, mentorship_areas, is_spotlighted, spotlight_note)
SELECT
  'alumni','Ngozi Adeyemi','ngozi.adeyemi@example.com',@pw,'Head of Product, Flutterwave','Flutterwave','Technology & Fintech','Lagos','Nigeria',2018,'Executive MBA',
  'Product leader focused on building payment infrastructure for African merchants. Passionate about mentoring early-career product managers.',
  'Product Strategy, Fintech, Payments, Agile Leadership','Product Management, Fintech Strategy','Payments, Financial Inclusion',
  'https://linkedin.com/in/ngoziadeyemi','active','public',1,'Product Management, Career Transitions',1,'Recognized for scaling Flutterwave''s merchant products across 6 African markets.'
 WHERE NOT EXISTS (SELECT 1 FROM users WHERE email = 'ngozi.adeyemi@example.com');

INSERT INTO users (role, name, email, password_hash, headline, company, industry, city, country, graduation_year, program, bio, skills, expertise_areas, business_interests, linkedin_url, status, profile_visibility, is_mentor, mentorship_areas)
SELECT
  'alumni','Emeka Nwosu','emeka.nwosu@example.com',@pw,'Investment Banking VP, Standard Chartered','Standard Chartered','Banking & Finance','Lagos','Nigeria',2016,'MBA',
  'Advising corporates on capital raising and M&A across West Africa.',
  'Investment Banking, M&A, Financial Modelling','Corporate Finance, Deal Structuring','Private Equity',
  'https://linkedin.com/in/emekanwosu','active','public',1,'Finance Careers, Interview Prep'
 WHERE NOT EXISTS (SELECT 1 FROM users WHERE email = 'emeka.nwosu@example.com');

INSERT INTO users (role, name, email, password_hash, headline, company, industry, city, country, graduation_year, program, bio, skills, expertise_areas, business_interests, linkedin_url, status, profile_visibility)
SELECT
  'alumni','Folake Ogundimu','folake.ogundimu@example.com',@pw,'Marketing Director, Nestle Nigeria','Nestle Nigeria','Consumer Goods','Lagos','Nigeria',2017,'MSc Marketing Management',
  'Building brand strategy for FMCG products across Nigeria and Ghana.',
  'Brand Management, Consumer Insights, Digital Marketing','FMCG Marketing','Retail, Consumer Products',
  'https://linkedin.com/in/folakeogundimu','active','public'
 WHERE NOT EXISTS (SELECT 1 FROM users WHERE email = 'folake.ogundimu@example.com');

INSERT INTO users (role, name, email, password_hash, headline, company, industry, city, country, graduation_year, program, bio, skills, expertise_areas, business_interests, linkedin_url, status, profile_visibility, is_mentor, mentorship_areas)
SELECT
  'alumni','Ibrahim Suleiman','ibrahim.suleiman@example.com',@pw,'Head of Operations, Dangote Group','Dangote Group','Manufacturing','Kano','Nigeria',2015,'Executive MBA',
  'Leading operational excellence programs across Dangote''s manufacturing plants.',
  'Operations Management, Supply Chain, Lean Six Sigma','Manufacturing Operations','Industrial Manufacturing',
  'https://linkedin.com/in/ibrahimsuleiman','active','public',1,'Operations, Manufacturing Careers'
 WHERE NOT EXISTS (SELECT 1 FROM users WHERE email = 'ibrahim.suleiman@example.com');

INSERT INTO users (role, name, email, password_hash, headline, company, industry, city, country, graduation_year, program, bio, skills, expertise_areas, business_interests, linkedin_url, status, profile_visibility)
SELECT
  'alumni','Chiamaka Umeh','chiamaka.umeh@example.com',@pw,'Senior Data Scientist, Andela','Andela','Technology & Fintech','Lagos','Nigeria',2020,'MSc Finance',
  'Applying machine learning to talent-matching problems across the Andela network.',
  'Data Science, Python, Machine Learning','Analytics, Fintech','EdTech, HR Tech',
  'https://linkedin.com/in/chiamakaumeh','active','public'
 WHERE NOT EXISTS (SELECT 1 FROM users WHERE email = 'chiamaka.umeh@example.com');

INSERT INTO users (role, name, email, password_hash, headline, company, industry, city, country, graduation_year, program, bio, skills, expertise_areas, business_interests, linkedin_url, status, profile_visibility, is_mentor, mentorship_areas, is_spotlighted, spotlight_note)
SELECT
  'alumni','Segun Afolabi','segun.afolabi@example.com',@pw,'Founder & CEO, Afolabi Logistics','Afolabi Logistics','Logistics & Supply Chain','Ibadan','Nigeria',2014,'MBA',
  'Built a last-mile delivery network serving over 40 cities in Nigeria.',
  'Entrepreneurship, Logistics, Fundraising','Startup Scaling, Logistics Tech','Logistics, Supply Chain',
  'https://linkedin.com/in/segunafolabi','active','public',1,'Entrepreneurship, Fundraising',1,'Grew Afolabi Logistics from 2 vans to a 200-vehicle fleet in five years.'
 WHERE NOT EXISTS (SELECT 1 FROM users WHERE email = 'segun.afolabi@example.com');

INSERT INTO users (role, name, email, password_hash, headline, company, industry, city, country, graduation_year, program, bio, skills, expertise_areas, business_interests, linkedin_url, status, profile_visibility)
SELECT
  'alumni','Blessing Etim','blessing.etim@example.com',@pw,'HR Business Partner, Airtel Africa','Airtel Africa','Telecommunications','Port Harcourt','Nigeria',2019,'MBA',
  'Partnering with business leaders on talent strategy across Airtel''s Nigerian operations.',
  'HR Strategy, Talent Management, Organizational Design','People Operations','Telecommunications',
  'https://linkedin.com/in/blessingetim','active','public'
 WHERE NOT EXISTS (SELECT 1 FROM users WHERE email = 'blessing.etim@example.com');

INSERT INTO users (role, name, email, password_hash, headline, company, industry, city, country, graduation_year, program, bio, skills, expertise_areas, business_interests, linkedin_url, status, profile_visibility, is_mentor, mentorship_areas)
SELECT
  'alumni','Yusuf Abdullahi','yusuf.abdullahi@example.com',@pw,'Regional Sales Manager, Total Energies','Total Energies','Energy','Abuja','Nigeria',2016,'Executive MBA',
  'Managing distributor relationships across Northern Nigeria''s energy sector.',
  'Sales Strategy, Key Account Management, Negotiation','Energy Sector Sales','Oil & Gas, Renewable Energy',
  'https://linkedin.com/in/yusufabdullahi','active','public',1,'Sales Careers, Negotiation Skills'
 WHERE NOT EXISTS (SELECT 1 FROM users WHERE email = 'yusuf.abdullahi@example.com');

INSERT INTO users (role, name, email, password_hash, headline, company, industry, city, country, graduation_year, program, bio, skills, expertise_areas, business_interests, linkedin_url, status, profile_visibility)
SELECT
  'alumni','Adaeze Chukwu','adaeze.chukwu@example.com',@pw,'Legal Counsel, Zenith Bank','Zenith Bank','Banking & Finance','Lagos','Nigeria',2018,'MBA',
  'Handling regulatory compliance and corporate governance for retail banking products.',
  'Corporate Law, Regulatory Compliance, Risk Management','Banking Law','Financial Regulation',
  'https://linkedin.com/in/adaezechukwu','active','public'
 WHERE NOT EXISTS (SELECT 1 FROM users WHERE email = 'adaeze.chukwu@example.com');

INSERT INTO users (role, name, email, password_hash, headline, company, industry, city, country, graduation_year, program, bio, skills, expertise_areas, business_interests, linkedin_url, status, profile_visibility)
SELECT
  'alumni','Kemi Ajayi','kemi.ajayi@example.com',@pw,'Product Marketing Lead, Paystack','Paystack','Technology & Fintech','Lagos','Nigeria',2021,'MSc Marketing Management',
  'Driving go-to-market strategy for Paystack''s merchant tools.',
  'Product Marketing, Growth, Fintech','Fintech Marketing','Payments',
  'https://linkedin.com/in/kemiajayi','active','public'
 WHERE NOT EXISTS (SELECT 1 FROM users WHERE email = 'kemi.ajayi@example.com');

INSERT INTO users (role, name, email, password_hash, headline, company, industry, city, country, graduation_year, program, bio, skills, expertise_areas, business_interests, linkedin_url, status, profile_visibility, is_mentor, mentorship_areas)
SELECT
  'alumni','Obinna Eze','obinna.eze@example.com',@pw,'Finance Director, PZ Cussons Nigeria','PZ Cussons Nigeria','Consumer Goods','Lagos','Nigeria',2015,'Executive MBA',
  'Overseeing financial planning and analysis for West African operations.',
  'Financial Planning, Budgeting, FP&A','Corporate Finance','FMCG Finance',
  'https://linkedin.com/in/obinnaeze','active','public',1,'Finance Careers'
 WHERE NOT EXISTS (SELECT 1 FROM users WHERE email = 'obinna.eze@example.com');

INSERT INTO users (role, name, email, password_hash, headline, company, industry, city, country, graduation_year, program, bio, skills, expertise_areas, business_interests, linkedin_url, status, profile_visibility)
SELECT
  'alumni','Halima Bello','halima.bello@example.com',@pw,'Program Manager, PwC Nigeria','PwC Nigeria','Professional Services','Abuja','Nigeria',2019,'MBA',
  'Managing public sector advisory engagements across Northern Nigeria.',
  'Program Management, Public Sector Advisory, Stakeholder Engagement','Consulting','Public Policy',
  'https://linkedin.com/in/halimabello','active','public'
 WHERE NOT EXISTS (SELECT 1 FROM users WHERE email = 'halima.bello@example.com');

INSERT INTO users (role, name, email, password_hash, headline, company, industry, city, country, graduation_year, program, bio, skills, expertise_areas, business_interests, linkedin_url, status, profile_visibility)
SELECT
  'alumni','Tobi Alabi','tobi.alabi@example.com',@pw,'Growth Lead, Bolt Nigeria','Bolt','Technology & Fintech','Lagos','Nigeria',2022,'MBA',
  'Scaling rider and driver acquisition across Bolt''s Nigerian cities.',
  'Growth Marketing, Marketplace Strategy, Analytics','Mobility, Marketplaces','Ride-hailing, Mobility',
  'https://linkedin.com/in/tobialabi','active','public'
 WHERE NOT EXISTS (SELECT 1 FROM users WHERE email = 'tobi.alabi@example.com');

INSERT INTO users (role, name, email, password_hash, headline, company, industry, city, country, graduation_year, program, bio, skills, expertise_areas, business_interests, linkedin_url, status, profile_visibility, is_mentor, mentorship_areas)
SELECT
  'alumni','Chinwe Okafor','chinwe.okafor@example.com',@pw,'Managing Partner, Okafor & Co.','Okafor & Co.','Professional Services','Enugu','Nigeria',2013,'DBA',
  'Running a boutique management consultancy serving mid-market African companies.',
  'Management Consulting, Strategy, Corporate Governance','Strategy Consulting','Consulting, Advisory Services',
  'https://linkedin.com/in/chinweokafor','active','public',1,'Consulting Careers, Starting a Consultancy'
 WHERE NOT EXISTS (SELECT 1 FROM users WHERE email = 'chinwe.okafor@example.com');

INSERT INTO users (role, name, email, password_hash, headline, company, industry, city, country, graduation_year, program, bio, skills, expertise_areas, business_interests, linkedin_url, status, profile_visibility)
SELECT
  'alumni','Musa Aliyu','musa.aliyu@example.com',@pw,'Plant Manager, Shell Nigeria','Shell Nigeria','Oil & Gas','Port Harcourt','Nigeria',2014,'Executive MBA',
  'Leading safety and production performance at Shell''s Niger Delta facilities.',
  'Operations, HSE, Project Management','Oil & Gas Operations','Energy',
  'https://linkedin.com/in/musaaliyu','active','public'
 WHERE NOT EXISTS (SELECT 1 FROM users WHERE email = 'musa.aliyu@example.com');

INSERT INTO users (role, name, email, password_hash, headline, company, industry, city, country, graduation_year, program, bio, skills, expertise_areas, business_interests, linkedin_url, status, profile_visibility)
SELECT
  'alumni','Funmilayo Salako','funmilayo.salako@example.com',@pw,'Product Manager, Jumia Nigeria','Jumia','Technology & Fintech','Lagos','Nigeria',2020,'MBA',
  'Managing the electronics category across Jumia''s Nigerian marketplace.',
  'E-commerce, Category Management, Vendor Relations','Retail Tech','E-commerce',
  'https://linkedin.com/in/funmilayosalako','active','public'
 WHERE NOT EXISTS (SELECT 1 FROM users WHERE email = 'funmilayo.salako@example.com');

INSERT INTO users (role, name, email, password_hash, headline, company, industry, city, country, graduation_year, program, bio, skills, expertise_areas, business_interests, linkedin_url, status, profile_visibility)
SELECT
  'alumni','David Okeke','david.okeke@example.com',@pw,'Regional Director, MTN Nigeria','MTN Nigeria','Telecommunications','Lagos','Nigeria',2012,'Executive MBA',
  'Overseeing commercial performance for MTN''s South-West Nigeria region.',
  'Telecom Strategy, P&L Management, Leadership','Telecommunications','Telecom, Digital Services',
  'https://linkedin.com/in/davidokeke','active','public'
 WHERE NOT EXISTS (SELECT 1 FROM users WHERE email = 'david.okeke@example.com');

INSERT INTO users (role, name, email, password_hash, headline, company, industry, city, country, graduation_year, program, bio, skills, expertise_areas, business_interests, linkedin_url, status, profile_visibility)
SELECT
  'alumni','Ronke Bamidele','ronke.bamidele@example.com',@pw,'Healthcare Consultant, WHO Africa','World Health Organization','Healthcare','Accra','Ghana',2017,'MBA',
  'Advising on health systems strengthening projects across West Africa.',
  'Healthcare Strategy, Public Health, Project Management','Health Systems','Public Health, NGO',
  'https://linkedin.com/in/ronkebamidele','active','public'
 WHERE NOT EXISTS (SELECT 1 FROM users WHERE email = 'ronke.bamidele@example.com');

INSERT INTO users (role, name, email, password_hash, headline, company, industry, city, country, graduation_year, program, bio, skills, expertise_areas, business_interests, linkedin_url, status, profile_visibility)
SELECT
  'alumni','Uche Iwu','uche.iwu@example.com',@pw,'Real Estate Investment Analyst, Actis','Actis','Real Estate','Lagos','Nigeria',2021,'MSc Finance',
  'Evaluating real estate investment opportunities across Sub-Saharan Africa.',
  'Real Estate Finance, Valuation, Market Research','Real Estate Investment','Real Estate, Private Equity',
  'https://linkedin.com/in/ucheiwu','active','public'
 WHERE NOT EXISTS (SELECT 1 FROM users WHERE email = 'uche.iwu@example.com');

INSERT INTO users (role, name, email, password_hash, headline, company, industry, city, country, graduation_year, program, bio, skills, expertise_areas, business_interests, linkedin_url, status, profile_visibility)
SELECT
  'alumni','Grace Effiong','grace.effiong@example.com',@pw,'Media Strategy Lead, Multichoice Nigeria','MultiChoice Nigeria','Media & Entertainment','Lagos','Nigeria',2019,'MBA',
  'Leading content and distribution strategy for DStv''s Nigerian audience.',
  'Media Strategy, Content Distribution, Partnerships','Media & Broadcasting','Media, Entertainment',
  'https://linkedin.com/in/graceeffiong','active','public'
 WHERE NOT EXISTS (SELECT 1 FROM users WHERE email = 'grace.effiong@example.com');

-- ---------------------------------------------------------------------
-- 7 additional companies
-- ---------------------------------------------------------------------
INSERT INTO companies (name, about, website, location)
SELECT 'Flutterwave','Payment infrastructure for African merchants, powering payments across 30+ countries.','https://flutterwave.com','Lagos, Nigeria' WHERE NOT EXISTS (SELECT 1 FROM companies WHERE name = 'Flutterwave');
INSERT INTO companies (name, about, website, location)
SELECT 'MTN Nigeria','Leading telecommunications provider connecting millions of Nigerians.','https://mtnonline.com','Lagos, Nigeria' WHERE NOT EXISTS (SELECT 1 FROM companies WHERE name = 'MTN Nigeria');
INSERT INTO companies (name, about, website, location)
SELECT 'Andela','Global technology talent network connecting African engineers to companies worldwide.','https://andela.com','Lagos, Nigeria' WHERE NOT EXISTS (SELECT 1 FROM companies WHERE name = 'Andela');
INSERT INTO companies (name, about, website, location)
SELECT 'Jumia Nigeria','Africa''s leading e-commerce platform.','https://jumia.com.ng','Lagos, Nigeria' WHERE NOT EXISTS (SELECT 1 FROM companies WHERE name = 'Jumia Nigeria');
INSERT INTO companies (name, about, website, location)
SELECT 'Paystack','Modern payment infrastructure for African businesses.','https://paystack.com','Lagos, Nigeria' WHERE NOT EXISTS (SELECT 1 FROM companies WHERE name = 'Paystack');
INSERT INTO companies (name, about, website, location)
SELECT 'Dangote Group','Africa''s largest industrial conglomerate spanning cement, sugar and refining.','https://dangote.com','Lagos, Nigeria' WHERE NOT EXISTS (SELECT 1 FROM companies WHERE name = 'Dangote Group');
INSERT INTO companies (name, about, website, location)
SELECT 'Total Energies Nigeria','Integrated energy company operating across Nigeria''s oil and gas value chain.','https://totalenergies.com','Port Harcourt, Nigeria' WHERE NOT EXISTS (SELECT 1 FROM companies WHERE name = 'Total Energies Nigeria');

-- ---------------------------------------------------------------------
-- 21 additional jobs (mix of admin-posted and alumni-posted)
-- ---------------------------------------------------------------------
INSERT INTO jobs (posted_by, title, company, company_id, location, job_type, description, apply_type, apply_value, is_featured, status, approval_status, posted_at, closing_date)
SELECT NULL,'Product Manager','Flutterwave',(SELECT id FROM companies WHERE name='Flutterwave'),'Lagos, Nigeria','Full-time','<p>Lead product strategy for Flutterwave''s merchant payment tools, working closely with engineering and design.</p>','email','careers@flutterwave.com',1,'open','approved','2026-08-01 09:00:00','2026-10-15' WHERE NOT EXISTS (SELECT 1 FROM jobs WHERE title='Product Manager' AND company='Flutterwave');

INSERT INTO jobs (posted_by, title, company, company_id, location, job_type, description, apply_type, apply_value, status, approval_status, posted_at, closing_date)
SELECT NULL,'Investment Banking Analyst','Standard Chartered',NULL,'Lagos, Nigeria','Full-time','<p>Support deal execution and financial modelling for corporate finance transactions across West Africa.</p>','email','careers@sc.com','open','approved','2026-08-02 09:00:00','2026-10-20' WHERE NOT EXISTS (SELECT 1 FROM jobs WHERE title='Investment Banking Analyst' AND company='Standard Chartered');

INSERT INTO jobs (posted_by, title, company, company_id, location, job_type, description, apply_type, apply_value, status, approval_status, posted_at, closing_date)
SELECT NULL,'Brand Manager','Nestle Nigeria',NULL,'Lagos, Nigeria','Full-time','<p>Own brand strategy and campaign execution for a portfolio of FMCG products.</p>','email','careers@ng.nestle.com','open','approved','2026-08-02 09:00:00','2026-10-25' WHERE NOT EXISTS (SELECT 1 FROM jobs WHERE title='Brand Manager' AND company='Nestle Nigeria');

INSERT INTO jobs (posted_by, title, company, company_id, location, job_type, description, apply_type, apply_value, status, approval_status, posted_at, closing_date)
SELECT NULL,'Operations Supervisor','Dangote Group',(SELECT id FROM companies WHERE name='Dangote Group'),'Kano, Nigeria','Full-time','<p>Supervise daily plant operations and drive continuous improvement initiatives.</p>','email','careers@dangote.com','open','approved','2026-08-03 09:00:00','2026-11-01' WHERE NOT EXISTS (SELECT 1 FROM jobs WHERE title='Operations Supervisor' AND company='Dangote Group');

INSERT INTO jobs (posted_by, title, company, company_id, location, job_type, description, apply_type, apply_value, status, approval_status, posted_at, closing_date)
SELECT NULL,'Data Analyst','Andela',(SELECT id FROM companies WHERE name='Andela'),'Lagos, Nigeria','Full-time','<p>Analyze talent-matching data to improve engineer placement outcomes across the Andela network.</p>','email','careers@andela.com','open','approved','2026-08-03 09:00:00','2026-11-05' WHERE NOT EXISTS (SELECT 1 FROM jobs WHERE title='Data Analyst' AND company='Andela');

INSERT INTO jobs (posted_by, title, company, company_id, location, job_type, description, apply_type, apply_value, status, approval_status, posted_at, closing_date)
SELECT (SELECT id FROM users WHERE email='segun.afolabi@example.com'),'Fleet Operations Manager','Afolabi Logistics',NULL,'Ibadan, Nigeria','Full-time','<p>Manage a growing fleet of delivery vehicles and lead our regional operations team. Great opportunity to join a fast-scaling alumni-founded logistics company.</p>','email','careers@afolabilogistics.example.com','open','approved','2026-08-04 09:00:00','2026-11-10' WHERE NOT EXISTS (SELECT 1 FROM jobs WHERE title='Fleet Operations Manager' AND company='Afolabi Logistics');

INSERT INTO jobs (posted_by, title, company, company_id, location, job_type, description, apply_type, apply_value, status, approval_status, posted_at, closing_date)
SELECT NULL,'HR Business Partner','Airtel Africa',NULL,'Port Harcourt, Nigeria','Full-time','<p>Partner with business leaders on talent strategy, performance management and org design.</p>','email','careers@airtel.africa','open','approved','2026-08-04 09:00:00','2026-11-12' WHERE NOT EXISTS (SELECT 1 FROM jobs WHERE title='HR Business Partner' AND company='Airtel Africa');

INSERT INTO jobs (posted_by, title, company, company_id, location, job_type, description, apply_type, apply_value, status, approval_status, posted_at, closing_date)
SELECT NULL,'Regional Sales Manager','Total Energies Nigeria',(SELECT id FROM companies WHERE name='Total Energies Nigeria'),'Abuja, Nigeria','Full-time','<p>Manage distributor relationships and drive sales growth across Northern Nigeria.</p>','email','careers@totalenergies.com','open','approved','2026-08-05 09:00:00','2026-11-15' WHERE NOT EXISTS (SELECT 1 FROM jobs WHERE title='Regional Sales Manager' AND company='Total Energies Nigeria');

INSERT INTO jobs (posted_by, title, company, company_id, location, job_type, description, apply_type, apply_value, status, approval_status, posted_at, closing_date)
SELECT NULL,'Compliance Officer','Zenith Bank',NULL,'Lagos, Nigeria','Full-time','<p>Ensure regulatory compliance across retail banking products and processes.</p>','email','careers@zenithbank.com','open','approved','2026-08-05 09:00:00','2026-11-18' WHERE NOT EXISTS (SELECT 1 FROM jobs WHERE title='Compliance Officer' AND company='Zenith Bank');

INSERT INTO jobs (posted_by, title, company, company_id, location, job_type, description, apply_type, apply_value, is_featured, status, approval_status, posted_at, closing_date)
SELECT NULL,'Growth Marketing Lead','Bolt',NULL,'Lagos, Nigeria','Full-time','<p>Own rider and driver acquisition strategy across Bolt''s Nigerian cities.</p>','link','https://bolt.eu/careers',1,'open','approved','2026-08-06 09:00:00','2026-11-20' WHERE NOT EXISTS (SELECT 1 FROM jobs WHERE title='Growth Marketing Lead' AND company='Bolt');

INSERT INTO jobs (posted_by, title, company, company_id, location, job_type, description, apply_type, apply_value, status, approval_status, posted_at, closing_date)
SELECT NULL,'Finance Manager','PZ Cussons Nigeria',NULL,'Lagos, Nigeria','Full-time','<p>Lead financial planning and analysis for West African operations.</p>','email','careers@pzcussons.com','open','approved','2026-08-06 09:00:00','2026-11-22' WHERE NOT EXISTS (SELECT 1 FROM jobs WHERE title='Finance Manager' AND company='PZ Cussons Nigeria');

INSERT INTO jobs (posted_by, title, company, company_id, location, job_type, description, apply_type, apply_value, status, approval_status, posted_at, closing_date)
SELECT NULL,'Management Consultant','PwC Nigeria',NULL,'Abuja, Nigeria','Full-time','<p>Deliver public sector advisory engagements across Northern Nigeria.</p>','email','careers@pwc.com','open','approved','2026-08-07 09:00:00','2026-11-25' WHERE NOT EXISTS (SELECT 1 FROM jobs WHERE title='Management Consultant' AND company='PwC Nigeria');

INSERT INTO jobs (posted_by, title, company, company_id, location, job_type, description, apply_type, apply_value, status, approval_status, posted_at, closing_date)
SELECT NULL,'Backend Software Engineer','Paystack',(SELECT id FROM companies WHERE name='Paystack'),'Lagos, Nigeria','Full-time','<p>Build and scale payment APIs used by thousands of African businesses.</p>','email','careers@paystack.com','open','approved','2026-08-07 09:00:00','2026-11-28' WHERE NOT EXISTS (SELECT 1 FROM jobs WHERE title='Backend Software Engineer' AND company='Paystack');

INSERT INTO jobs (posted_by, title, company, company_id, location, job_type, description, apply_type, apply_value, status, approval_status, posted_at, closing_date)
SELECT NULL,'Category Manager','Jumia Nigeria',(SELECT id FROM companies WHERE name='Jumia Nigeria'),'Lagos, Nigeria','Full-time','<p>Manage vendor relationships and category growth for electronics on Jumia''s marketplace.</p>','email','careers@jumia.com.ng','open','approved','2026-08-08 09:00:00','2026-12-01' WHERE NOT EXISTS (SELECT 1 FROM jobs WHERE title='Category Manager' AND company='Jumia Nigeria');

INSERT INTO jobs (posted_by, title, company, company_id, location, job_type, description, apply_type, apply_value, status, approval_status, posted_at, closing_date)
SELECT NULL,'Plant Engineer','Shell Nigeria',NULL,'Port Harcourt, Nigeria','Full-time','<p>Support safety and production performance at Niger Delta facilities.</p>','email','careers@shell.com','open','approved','2026-08-08 09:00:00','2026-12-05' WHERE NOT EXISTS (SELECT 1 FROM jobs WHERE title='Plant Engineer' AND company='Shell Nigeria');

INSERT INTO jobs (posted_by, title, company, company_id, location, job_type, description, apply_type, apply_value, status, approval_status, posted_at, closing_date)
SELECT NULL,'Enterprise Account Manager','MTN Nigeria',(SELECT id FROM companies WHERE name='MTN Nigeria'),'Lagos, Nigeria','Full-time','<p>Manage enterprise client relationships across MTN''s South-West Nigeria region.</p>','email','careers@mtn.com','open','approved','2026-08-09 09:00:00','2026-12-08' WHERE NOT EXISTS (SELECT 1 FROM jobs WHERE title='Enterprise Account Manager' AND company='MTN Nigeria');

INSERT INTO jobs (posted_by, title, company, company_id, location, job_type, description, apply_type, apply_value, status, approval_status, posted_at, closing_date)
SELECT NULL,'Health Systems Program Officer','World Health Organization',NULL,'Accra, Ghana','Full-time','<p>Support health systems strengthening projects across West Africa.</p>','email','careers@who.int','open','approved','2026-08-09 09:00:00','2026-12-10' WHERE NOT EXISTS (SELECT 1 FROM jobs WHERE title='Health Systems Program Officer' AND company='World Health Organization');

INSERT INTO jobs (posted_by, title, company, company_id, location, job_type, description, apply_type, apply_value, status, approval_status, posted_at, closing_date)
SELECT NULL,'Real Estate Investment Analyst','Actis',NULL,'Lagos, Nigeria','Full-time','<p>Evaluate real estate investment opportunities across Sub-Saharan Africa.</p>','email','careers@act.is','open','approved','2026-08-10 09:00:00','2026-12-12' WHERE NOT EXISTS (SELECT 1 FROM jobs WHERE title='Real Estate Investment Analyst' AND company='Actis');

INSERT INTO jobs (posted_by, title, company, company_id, location, job_type, description, apply_type, apply_value, status, approval_status, posted_at, closing_date)
SELECT NULL,'Media Partnerships Manager','MultiChoice Nigeria',NULL,'Lagos, Nigeria','Full-time','<p>Lead content and distribution partnerships for DStv''s Nigerian audience.</p>','email','careers@multichoice.com','open','approved','2026-08-10 09:00:00','2026-12-15' WHERE NOT EXISTS (SELECT 1 FROM jobs WHERE title='Media Partnerships Manager' AND company='MultiChoice Nigeria');

INSERT INTO jobs (posted_by, title, company, company_id, location, job_type, description, apply_type, apply_value, status, approval_status, posted_at, closing_date)
SELECT NULL,'Business Development Executive','KPMG Nigeria',(SELECT id FROM companies WHERE name='KPMG Nigeria'),'Lagos, Nigeria','Full-time','<p>This listing has closed &mdash; kept as a sample of the automatic closing-date behavior.</p>','email','careers@kpmg.com','open','approved','2026-06-01 09:00:00','2026-07-01' WHERE NOT EXISTS (SELECT 1 FROM jobs WHERE title='Business Development Executive' AND company='KPMG Nigeria');

INSERT INTO jobs (posted_by, title, company, company_id, location, job_type, description, apply_type, apply_value, status, approval_status, closing_date)
SELECT (SELECT id FROM users WHERE email='ngozi.adeyemi@example.com'),'Junior Consultant','Deloitte West Africa',(SELECT id FROM companies WHERE name='Deloitte West Africa'),'Lagos, Nigeria','Full-time','<p>Support strategy engagements for consumer and financial services clients. Submitted by an alumna for admin review.</p>','email','careers@deloitte.com','open','pending','2026-12-20' WHERE NOT EXISTS (SELECT 1 FROM jobs WHERE title='Junior Consultant' AND company='Deloitte West Africa');

-- job categories: Consulting=5, Banking & Finance=6, Technology=7, Marketing=8, Operations=9
INSERT INTO job_category (job_id, category_id) SELECT j.id, 7 FROM jobs j WHERE j.title='Product Manager' AND j.company='Flutterwave' AND NOT EXISTS (SELECT 1 FROM job_category WHERE job_id=j.id AND category_id=7);
INSERT INTO job_category (job_id, category_id) SELECT j.id, 6 FROM jobs j WHERE j.title='Investment Banking Analyst' AND NOT EXISTS (SELECT 1 FROM job_category WHERE job_id=j.id AND category_id=6);
INSERT INTO job_category (job_id, category_id) SELECT j.id, 8 FROM jobs j WHERE j.title='Brand Manager' AND NOT EXISTS (SELECT 1 FROM job_category WHERE job_id=j.id AND category_id=8);
INSERT INTO job_category (job_id, category_id) SELECT j.id, 9 FROM jobs j WHERE j.title='Operations Supervisor' AND NOT EXISTS (SELECT 1 FROM job_category WHERE job_id=j.id AND category_id=9);
INSERT INTO job_category (job_id, category_id) SELECT j.id, 7 FROM jobs j WHERE j.title='Data Analyst' AND NOT EXISTS (SELECT 1 FROM job_category WHERE job_id=j.id AND category_id=7);
INSERT INTO job_category (job_id, category_id) SELECT j.id, 9 FROM jobs j WHERE j.title='Fleet Operations Manager' AND NOT EXISTS (SELECT 1 FROM job_category WHERE job_id=j.id AND category_id=9);
INSERT INTO job_category (job_id, category_id) SELECT j.id, 9 FROM jobs j WHERE j.title='HR Business Partner' AND NOT EXISTS (SELECT 1 FROM job_category WHERE job_id=j.id AND category_id=9);
INSERT INTO job_category (job_id, category_id) SELECT j.id, 8 FROM jobs j WHERE j.title='Regional Sales Manager' AND NOT EXISTS (SELECT 1 FROM job_category WHERE job_id=j.id AND category_id=8);
INSERT INTO job_category (job_id, category_id) SELECT j.id, 6 FROM jobs j WHERE j.title='Compliance Officer' AND NOT EXISTS (SELECT 1 FROM job_category WHERE job_id=j.id AND category_id=6);
INSERT INTO job_category (job_id, category_id) SELECT j.id, 8 FROM jobs j WHERE j.title='Growth Marketing Lead' AND NOT EXISTS (SELECT 1 FROM job_category WHERE job_id=j.id AND category_id=8);
INSERT INTO job_category (job_id, category_id) SELECT j.id, 6 FROM jobs j WHERE j.title='Finance Manager' AND NOT EXISTS (SELECT 1 FROM job_category WHERE job_id=j.id AND category_id=6);
INSERT INTO job_category (job_id, category_id) SELECT j.id, 5 FROM jobs j WHERE j.title='Management Consultant' AND NOT EXISTS (SELECT 1 FROM job_category WHERE job_id=j.id AND category_id=5);
INSERT INTO job_category (job_id, category_id) SELECT j.id, 7 FROM jobs j WHERE j.title='Backend Software Engineer' AND NOT EXISTS (SELECT 1 FROM job_category WHERE job_id=j.id AND category_id=7);
INSERT INTO job_category (job_id, category_id) SELECT j.id, 8 FROM jobs j WHERE j.title='Category Manager' AND NOT EXISTS (SELECT 1 FROM job_category WHERE job_id=j.id AND category_id=8);
INSERT INTO job_category (job_id, category_id) SELECT j.id, 9 FROM jobs j WHERE j.title='Plant Engineer' AND NOT EXISTS (SELECT 1 FROM job_category WHERE job_id=j.id AND category_id=9);
INSERT INTO job_category (job_id, category_id) SELECT j.id, 7 FROM jobs j WHERE j.title='Enterprise Account Manager' AND NOT EXISTS (SELECT 1 FROM job_category WHERE job_id=j.id AND category_id=7);
INSERT INTO job_category (job_id, category_id) SELECT j.id, 9 FROM jobs j WHERE j.title='Health Systems Program Officer' AND NOT EXISTS (SELECT 1 FROM job_category WHERE job_id=j.id AND category_id=9);
INSERT INTO job_category (job_id, category_id) SELECT j.id, 6 FROM jobs j WHERE j.title='Real Estate Investment Analyst' AND NOT EXISTS (SELECT 1 FROM job_category WHERE job_id=j.id AND category_id=6);
INSERT INTO job_category (job_id, category_id) SELECT j.id, 8 FROM jobs j WHERE j.title='Media Partnerships Manager' AND NOT EXISTS (SELECT 1 FROM job_category WHERE job_id=j.id AND category_id=8);
INSERT INTO job_category (job_id, category_id) SELECT j.id, 5 FROM jobs j WHERE j.title='Business Development Executive' AND j.company='KPMG Nigeria' AND NOT EXISTS (SELECT 1 FROM job_category WHERE job_id=j.id AND category_id=5);
INSERT INTO job_category (job_id, category_id) SELECT j.id, 5 FROM jobs j WHERE j.title='Junior Consultant' AND NOT EXISTS (SELECT 1 FROM job_category WHERE job_id=j.id AND category_id=5);

-- ---------------------------------------------------------------------
-- 22 additional events (mix of past/future, in-person/virtual)
-- ---------------------------------------------------------------------
INSERT INTO events (title, description, location, is_virtual, event_date, event_time)
SELECT 'Lagos Alumni Homecoming','A homecoming celebration bringing together alumni across every graduating class.','Lagos, Nigeria',0,'2026-09-12','4:00 PM WAT' WHERE NOT EXISTS (SELECT 1 FROM events WHERE title='Lagos Alumni Homecoming');
INSERT INTO events (title, description, location, is_virtual, event_date, event_time)
SELECT 'Global Virtual Town Hall','Live update from the Dean on RBSN''s global strategy, open to all alumni worldwide.','Online',1,'2026-09-20','6:00 PM WAT' WHERE NOT EXISTS (SELECT 1 FROM events WHERE title='Global Virtual Town Hall');
INSERT INTO events (title, description, location, is_virtual, event_date, event_time)
SELECT 'Women in Leadership Breakfast','A morning of conversation and networking with senior women leaders from the alumni network.','Lagos, Nigeria',0,'2026-09-25','8:00 AM WAT' WHERE NOT EXISTS (SELECT 1 FROM events WHERE title='Women in Leadership Breakfast');
INSERT INTO events (title, description, location, is_virtual, event_date, event_time)
SELECT 'Fintech Founders Meetup','Alumni founders and operators in fintech share lessons from scaling African startups.','Lagos, Nigeria',0,'2026-10-02','5:30 PM WAT' WHERE NOT EXISTS (SELECT 1 FROM events WHERE title='Fintech Founders Meetup');
INSERT INTO events (title, description, location, is_virtual, event_date, event_time)
SELECT 'MBA Class of 2016 Reunion','A ten-year reunion celebration for the MBA Class of 2016.','Lagos, Nigeria',0,'2026-10-10','6:00 PM WAT' WHERE NOT EXISTS (SELECT 1 FROM events WHERE title='MBA Class of 2016 Reunion');
INSERT INTO events (title, description, location, is_virtual, event_date, event_time)
SELECT 'Executive Leadership Masterclass','A half-day masterclass on strategic leadership, delivered by RBSN faculty.','Abuja, Nigeria',0,'2026-10-16','9:00 AM WAT' WHERE NOT EXISTS (SELECT 1 FROM events WHERE title='Executive Leadership Masterclass');
INSERT INTO events (title, description, location, is_virtual, event_date, event_time)
SELECT 'Data-Driven Decision Making Webinar','Learn practical frameworks for using data to guide business decisions.','Online',1,'2026-10-20','5:00 PM WAT' WHERE NOT EXISTS (SELECT 1 FROM events WHERE title='Data-Driven Decision Making Webinar');
INSERT INTO events (title, description, location, is_virtual, event_date, event_time)
SELECT 'Accra Alumni Chapter Mixer','An evening mixer for the growing Accra alumni chapter.','Accra, Ghana',0,'2026-10-28','6:30 PM GMT' WHERE NOT EXISTS (SELECT 1 FROM events WHERE title='Accra Alumni Chapter Mixer');
INSERT INTO events (title, description, location, is_virtual, event_date, event_time)
SELECT 'Sustainable Business Forum','A forum exploring ESG and sustainable business practices across African industries.','Lagos, Nigeria',0,'2026-11-04','10:00 AM WAT' WHERE NOT EXISTS (SELECT 1 FROM events WHERE title='Sustainable Business Forum');
INSERT INTO events (title, description, location, is_virtual, event_date, event_time)
SELECT 'Careers in Consulting Panel','A panel of alumni consultants share how to break into and thrive in consulting.','Online',1,'2026-11-08','5:00 PM WAT' WHERE NOT EXISTS (SELECT 1 FROM events WHERE title='Careers in Consulting Panel');
INSERT INTO events (title, description, location, is_virtual, event_date, event_time)
SELECT 'Port Harcourt Energy Sector Roundtable','A roundtable discussion on the future of Nigeria''s energy sector.','Port Harcourt, Nigeria',0,'2026-11-14','2:00 PM WAT' WHERE NOT EXISTS (SELECT 1 FROM events WHERE title='Port Harcourt Energy Sector Roundtable');
INSERT INTO events (title, description, location, is_virtual, event_date, event_time)
SELECT 'Global MBA Info Session','Prospective students and alumni ambassadors discuss the Global MBA programme.','Online',1,'2026-11-19','4:00 PM WAT' WHERE NOT EXISTS (SELECT 1 FROM events WHERE title='Global MBA Info Session');
INSERT INTO events (title, description, location, is_virtual, event_date, event_time)
SELECT 'RBSN Giving Day Gala','An evening gala celebrating donors and scholarship recipients.','Lagos, Nigeria',0,'2026-11-27','7:00 PM WAT' WHERE NOT EXISTS (SELECT 1 FROM events WHERE title='RBSN Giving Day Gala');
INSERT INTO events (title, description, location, is_virtual, event_date, event_time)
SELECT 'Digital Marketing Masterclass','Hands-on session covering growth marketing tactics for African markets.','Online',1,'2026-12-03','5:00 PM WAT' WHERE NOT EXISTS (SELECT 1 FROM events WHERE title='Digital Marketing Masterclass');
INSERT INTO events (title, description, location, is_virtual, event_date, event_time)
SELECT 'Kano Business Leaders Breakfast','A breakfast meetup for alumni and business leaders in Northern Nigeria.','Kano, Nigeria',0,'2026-12-09','8:00 AM WAT' WHERE NOT EXISTS (SELECT 1 FROM events WHERE title='Kano Business Leaders Breakfast');
INSERT INTO events (title, description, location, is_virtual, event_date, event_time)
SELECT 'MBA Class of 2020 Reunion','Reunion celebration for the MBA Class of 2020.','Lagos, Nigeria',0,'2026-12-14','6:00 PM WAT' WHERE NOT EXISTS (SELECT 1 FROM events WHERE title='MBA Class of 2020 Reunion');
INSERT INTO events (title, description, location, is_virtual, event_date, event_time)
SELECT 'Year-End Alumni Networking Cocktail','Close out the year with fellow alumni over cocktails and canapes.','Lagos, Nigeria',0,'2026-12-19','7:00 PM WAT' WHERE NOT EXISTS (SELECT 1 FROM events WHERE title='Year-End Alumni Networking Cocktail');
INSERT INTO events (title, description, location, is_virtual, event_date, event_time)
SELECT 'New Year Leadership Kickoff Webinar','Start the year with practical leadership goal-setting frameworks.','Online',1,'2027-01-14','5:00 PM WAT' WHERE NOT EXISTS (SELECT 1 FROM events WHERE title='New Year Leadership Kickoff Webinar');
INSERT INTO events (title, description, location, is_virtual, event_date, event_time)
SELECT 'DBA Research Forum 2025','Doctoral alumni present ongoing research to the wider academic community.','Abuja, Nigeria',0,'2027-01-22','10:00 AM WAT' WHERE NOT EXISTS (SELECT 1 FROM events WHERE title='DBA Research Forum 2025');
INSERT INTO events (title, description, location, is_virtual, event_date, event_time)
SELECT 'Alumni Leadership Summit 2025','RBSN''s flagship annual summit bringing together alumni leaders from across the continent.','Lagos, Nigeria',0,'2027-02-05','9:00 AM WAT' WHERE NOT EXISTS (SELECT 1 FROM events WHERE title='Alumni Leadership Summit 2025');
INSERT INTO events (title, description, location, is_virtual, event_date, event_time)
SELECT 'Class of 2015 Anniversary Dinner','A dinner celebration marking a decade since graduation.','Lagos, Nigeria',0,'2026-06-10','7:00 PM WAT' WHERE NOT EXISTS (SELECT 1 FROM events WHERE title='Class of 2015 Anniversary Dinner');
INSERT INTO events (title, description, location, is_virtual, event_date, event_time)
SELECT 'Innovation & Entrepreneurship Bootcamp','A weekend bootcamp for alumni exploring new ventures.','Lagos, Nigeria',0,'2026-07-15','9:00 AM WAT' WHERE NOT EXISTS (SELECT 1 FROM events WHERE title='Innovation & Entrepreneurship Bootcamp');

-- ---------------------------------------------------------------------
-- 20 additional news posts
-- ---------------------------------------------------------------------
INSERT INTO news_posts (title, slug, excerpt, body, image, author, status, published_at)
SELECT 'RBSN Alumni Launch Pan-African Mentorship Network','rbsn-alumni-launch-pan-african-mentorship-network','A group of alumni have launched a continent-wide mentorship initiative connecting senior leaders with early-career professionals.','<p>A group of RBSN alumni across six countries have launched a new mentorship network designed to pair senior leaders with early-career professionals across the continent.</p><p>The initiative, which already has over 50 mentor-mentee pairs, focuses on career transitions, leadership development and entrepreneurship.</p>','https://images.unsplash.com/photo-1521737604893-d14cc237f11d?auto=format&fit=crop&w=900&q=80','RBSN Team','published','2026-08-19 09:00:00' WHERE NOT EXISTS (SELECT 1 FROM news_posts WHERE slug='rbsn-alumni-launch-pan-african-mentorship-network');

INSERT INTO news_posts (title, slug, excerpt, body, image, author, status, published_at)
SELECT 'How Three Alumni Are Reshaping Nigerian Fintech','how-three-alumni-are-reshaping-nigerian-fintech','From payments to lending, RBSN alumni are building some of Nigeria''s most talked-about fintech products.','<p>Nigeria''s fintech boom has no shortage of RBSN fingerprints on it. We caught up with three alumni building products used by millions of Nigerians every day.</p><p>Their advice for aspiring founders: start with a painfully specific problem, and let the market tell you when you''re ready to expand.</p>','https://images.unsplash.com/photo-1556761175-5973dc0f32e7?auto=format&fit=crop&w=900&q=80','RBSN Team','published','2026-08-17 09:00:00' WHERE NOT EXISTS (SELECT 1 FROM news_posts WHERE slug='how-three-alumni-are-reshaping-nigerian-fintech');

INSERT INTO news_posts (title, slug, excerpt, body, image, author, status, published_at)
SELECT 'Salary Negotiation Tactics for Mid-Career Professionals','salary-negotiation-tactics-for-mid-career-professionals','Practical, research-backed tactics for negotiating your next offer with confidence.','<p>Negotiating salary can feel uncomfortable, but preparation changes everything. Here are five tactics our career services team recommends to mid-career alumni.</p><p>Always benchmark against market data, and never accept the first offer without at least one counter.</p>','https://images.unsplash.com/photo-1454165804606-c3d57bc86b40?auto=format&fit=crop&w=900&q=80','RBSN Career Services','published','2026-08-15 09:00:00' WHERE NOT EXISTS (SELECT 1 FROM news_posts WHERE slug='salary-negotiation-tactics-for-mid-career-professionals');

INSERT INTO news_posts (title, slug, excerpt, body, image, author, status, published_at)
SELECT 'Building a Personal Brand on LinkedIn: A Guide for Alumni','building-a-personal-brand-on-linkedin-guide-for-alumni','A step-by-step guide to using LinkedIn intentionally to grow your professional reputation.','<p>Your LinkedIn profile is often the first impression a recruiter or collaborator gets. This guide walks through positioning, content strategy and network-building.</p>','https://images.unsplash.com/photo-1521791136064-7986c2920216?auto=format&fit=crop&w=900&q=80','RBSN Career Services','published','2026-08-13 09:00:00' WHERE NOT EXISTS (SELECT 1 FROM news_posts WHERE slug='building-a-personal-brand-on-linkedin-guide-for-alumni');

INSERT INTO news_posts (title, slug, excerpt, body, image, author, status, published_at)
SELECT 'Remote Work Trends Reshaping African Offices','remote-work-trends-reshaping-african-offices','What hybrid and remote work adoption means for African employers and talent.','<p>As global companies compete for African tech talent, hybrid work policies are becoming a key differentiator. We look at what''s changing and why it matters.</p>','https://images.unsplash.com/photo-1522071820081-009f0129c71c?auto=format&fit=crop&w=900&q=80','RBSN Team','published','2026-08-11 09:00:00' WHERE NOT EXISTS (SELECT 1 FROM news_posts WHERE slug='remote-work-trends-reshaping-african-offices');

INSERT INTO news_posts (title, slug, excerpt, body, image, author, status, published_at)
SELECT 'ESG Investing: What African Businesses Need to Know','esg-investing-what-african-businesses-need-to-know','A primer on environmental, social and governance investing and its growing relevance in Africa.','<p>ESG considerations are increasingly shaping how global capital flows into African markets. Here''s what business leaders need to understand.</p>','https://images.unsplash.com/photo-1600880292203-757bb62b4baf?auto=format&fit=crop&w=900&q=80','RBSN Team','published','2026-08-09 09:00:00' WHERE NOT EXISTS (SELECT 1 FROM news_posts WHERE slug='esg-investing-what-african-businesses-need-to-know');

INSERT INTO news_posts (title, slug, excerpt, body, image, author, status, published_at)
SELECT 'RBSN Welcomes New Dean of Executive Education','rbsn-welcomes-new-dean-of-executive-education','RBSN announces new leadership for its Executive Education division.','<p>RBSN is pleased to welcome a new Dean of Executive Education, bringing two decades of experience in leadership development across Africa and Europe.</p>','https://images.unsplash.com/photo-1552664730-d307ca884978?auto=format&fit=crop&w=900&q=80','RBSN Communications','published','2026-08-07 09:00:00' WHERE NOT EXISTS (SELECT 1 FROM news_posts WHERE slug='rbsn-welcomes-new-dean-of-executive-education');

INSERT INTO news_posts (title, slug, excerpt, body, image, author, status, published_at)
SELECT 'Class of 2024 Graduation Ceremony Highlights','class-of-2024-graduation-ceremony-highlights','Photos and highlights from this year''s graduation ceremony in Lagos.','<p>Over 200 graduates crossed the stage this year as RBSN celebrated the Class of 2024 at a ceremony in Lagos.</p>','https://images.unsplash.com/photo-1523240795612-9a054b0db644?auto=format&fit=crop&w=900&q=80','RBSN Communications','published','2026-08-04 09:00:00' WHERE NOT EXISTS (SELECT 1 FROM news_posts WHERE slug='class-of-2024-graduation-ceremony-highlights');

INSERT INTO news_posts (title, slug, excerpt, body, image, author, status, published_at)
SELECT 'From Banking to Building: Ibrahim Suleiman''s Career Pivot','from-banking-to-building-ibrahim-suleimans-career-pivot','How one alumnus moved from banking into industrial operations leadership.','<p>Ibrahim Suleiman spent his first decade in banking before pivoting into operations leadership at one of Africa''s largest industrial conglomerates. Here''s how he made the leap.</p>','https://images.unsplash.com/photo-1507679799987-c73779587ccf?auto=format&fit=crop&w=900&q=80','RBSN Team','published','2026-08-02 09:00:00' WHERE NOT EXISTS (SELECT 1 FROM news_posts WHERE slug='from-banking-to-building-ibrahim-suleimans-career-pivot');

INSERT INTO news_posts (title, slug, excerpt, body, image, author, status, published_at)
SELECT '7 Networking Habits of Highly Successful Alumni','7-networking-habits-of-highly-successful-alumni','Small, consistent habits that compound into a powerful professional network.','<p>We surveyed alumni across industries to find the networking habits that consistently pay off over a career. Number one: follow up within 48 hours.</p>','https://images.unsplash.com/photo-1573164713988-8665fc963095?auto=format&fit=crop&w=900&q=80','RBSN Career Services','published','2026-07-30 09:00:00' WHERE NOT EXISTS (SELECT 1 FROM news_posts WHERE slug='7-networking-habits-of-highly-successful-alumni');

INSERT INTO news_posts (title, slug, excerpt, body, image, author, status, published_at)
SELECT 'The Rise of B2B SaaS in West Africa','the-rise-of-b2b-saas-in-west-africa','A new wave of software companies is targeting West African businesses directly.','<p>Beyond consumer fintech, a new generation of B2B SaaS startups is emerging to serve African businesses with tools for payroll, logistics and compliance.</p>','https://images.unsplash.com/photo-1560250097-0b93528c311a?auto=format&fit=crop&w=900&q=80','RBSN Team','published','2026-07-27 09:00:00' WHERE NOT EXISTS (SELECT 1 FROM news_posts WHERE slug='the-rise-of-b2b-saas-in-west-africa');

INSERT INTO news_posts (title, slug, excerpt, body, image, author, status, published_at)
SELECT 'RBSN Partners with Local Startups for Innovation Lab','rbsn-partners-with-local-startups-for-innovation-lab','A new innovation lab will connect students and alumni with early-stage startups.','<p>RBSN has announced a partnership with a cohort of Lagos-based startups to launch an innovation lab, giving students and alumni hands-on exposure to early-stage venture building.</p>','https://images.unsplash.com/photo-1519389950473-47ba0277781c?auto=format&fit=crop&w=900&q=80','RBSN Communications','published','2026-07-24 09:00:00' WHERE NOT EXISTS (SELECT 1 FROM news_posts WHERE slug='rbsn-partners-with-local-startups-for-innovation-lab');

INSERT INTO news_posts (title, slug, excerpt, body, image, author, status, published_at)
SELECT 'Navigating Your First 90 Days in a New Leadership Role','navigating-your-first-90-days-in-a-new-leadership-role','A practical framework for making an impact without overreaching in your first quarter.','<p>The first 90 days in a new leadership role set the tone for everything that follows. Here''s a framework our alumni coaches recommend.</p>','https://images.unsplash.com/photo-1486406146926-c627a92ad1ab?auto=format&fit=crop&w=900&q=80','RBSN Career Services','published','2026-07-21 09:00:00' WHERE NOT EXISTS (SELECT 1 FROM news_posts WHERE slug='navigating-your-first-90-days-in-a-new-leadership-role');

INSERT INTO news_posts (title, slug, excerpt, body, image, author, status, published_at)
SELECT 'Women Leading Africa''s Energy Transition','women-leading-africas-energy-transition','Profiling the women driving renewable energy strategy across the continent.','<p>From utility-scale solar to distributed energy access, women are increasingly leading Africa''s energy transition. We profile a few standout leaders, including several RBSN alumni.</p>','https://images.unsplash.com/photo-1509391366360-2e959784a276?auto=format&fit=crop&w=900&q=80','RBSN Team','published','2026-07-18 09:00:00' WHERE NOT EXISTS (SELECT 1 FROM news_posts WHERE slug='women-leading-africas-energy-transition');

INSERT INTO news_posts (title, slug, excerpt, body, image, author, status, published_at)
SELECT 'Alumni Spotlight: Chinwe Okafor on Building a Consultancy','alumni-spotlight-chinwe-okafor-on-building-a-consultancy','From corporate strategy to founding her own boutique consultancy.','<p>After over a decade in corporate strategy, Chinwe Okafor took the leap to found her own management consultancy. She shares what she wishes she''d known on day one.</p>','https://images.unsplash.com/photo-1543269865-cbf427effbad?auto=format&fit=crop&w=900&q=80','RBSN Team','published','2026-07-15 09:00:00' WHERE NOT EXISTS (SELECT 1 FROM news_posts WHERE slug='alumni-spotlight-chinwe-okafor-on-building-a-consultancy');

INSERT INTO news_posts (title, slug, excerpt, body, image, author, status, published_at)
SELECT 'Digital Payments Adoption Accelerates Across Nigeria','digital-payments-adoption-accelerates-across-nigeria','New data shows digital payment volumes continuing their sharp rise.','<p>Digital payment volumes across Nigeria have continued their multi-year climb, driven by fintech innovation and improving smartphone penetration.</p>','https://images.unsplash.com/photo-1563013544-824ae1b704d3?auto=format&fit=crop&w=900&q=80','RBSN Team','published','2026-07-12 09:00:00' WHERE NOT EXISTS (SELECT 1 FROM news_posts WHERE slug='digital-payments-adoption-accelerates-across-nigeria');

INSERT INTO news_posts (title, slug, excerpt, body, image, author, status, published_at)
SELECT 'RBSN Ranked Among Top Business Schools in Africa','rbsn-ranked-among-top-business-schools-in-africa','RBSN climbs in this year''s continental business school rankings.','<p>Rome Business School Nigeria has been ranked among the top business schools in Africa, citing strong alumni outcomes and employer satisfaction.</p>','https://images.unsplash.com/photo-1523050854058-8df90110c9f1?auto=format&fit=crop&w=900&q=80','RBSN Communications','published','2026-07-09 09:00:00' WHERE NOT EXISTS (SELECT 1 FROM news_posts WHERE slug='rbsn-ranked-among-top-business-schools-in-africa');

INSERT INTO news_posts (title, slug, excerpt, body, image, author, status, published_at)
SELECT 'How to Ace a Case Interview: Tips from RBSN Alumni','how-to-ace-a-case-interview-tips-from-rbsn-alumni','Consulting alumni share their preparation strategies for case interviews.','<p>Breaking into consulting often hinges on the case interview. Alumni now working at top firms share how they prepared and what they wish they''d practiced more.</p>','https://images.unsplash.com/photo-1552581234-26160f608093?auto=format&fit=crop&w=900&q=80','RBSN Career Services','published','2026-07-06 09:00:00' WHERE NOT EXISTS (SELECT 1 FROM news_posts WHERE slug='how-to-ace-a-case-interview-tips-from-rbsn-alumni');

INSERT INTO news_posts (title, slug, excerpt, body, image, author, status, published_at)
SELECT 'The Future of Work: Skills Employers Want in 2026','the-future-of-work-skills-employers-want-in-2026','A look at the skills employers are prioritizing this year, based on alumni hiring data.','<p>Drawing on hiring trends from our own job board, we break down the top skills employers are prioritizing for 2026 &mdash; from AI literacy to change management.</p>','https://images.unsplash.com/photo-1531482615713-2afd69097998?auto=format&fit=crop&w=900&q=80','RBSN Team','published','2026-07-03 09:00:00' WHERE NOT EXISTS (SELECT 1 FROM news_posts WHERE slug='the-future-of-work-skills-employers-want-in-2026');

INSERT INTO news_posts (title, slug, excerpt, body, image, author, status, published_at)
SELECT 'Alumni Giving Day Raises Record Funds for Scholarships','alumni-giving-day-raises-record-funds-for-scholarships','This year''s Giving Day set a new fundraising record for the scholarship fund.','<p>Thanks to record participation from alumni worldwide, this year''s Giving Day raised more than ever before for the RBSN Scholarship Fund.</p>','https://images.unsplash.com/photo-1521791136064-7986c2920216?auto=format&fit=crop&w=900&q=80','RBSN Communications','published','2026-06-28 09:00:00' WHERE NOT EXISTS (SELECT 1 FROM news_posts WHERE slug='alumni-giving-day-raises-record-funds-for-scholarships');

-- news categories: Alumni Stories=1, Career Tips=2, Industry Insights=3, Campus News=4
INSERT INTO news_category (news_post_id, category_id) SELECT n.id, 1 FROM news_posts n WHERE n.slug='rbsn-alumni-launch-pan-african-mentorship-network' AND NOT EXISTS (SELECT 1 FROM news_category WHERE news_post_id=n.id AND category_id=1);
INSERT INTO news_category (news_post_id, category_id) SELECT n.id, 1 FROM news_posts n WHERE n.slug='how-three-alumni-are-reshaping-nigerian-fintech' AND NOT EXISTS (SELECT 1 FROM news_category WHERE news_post_id=n.id AND category_id=1);
INSERT INTO news_category (news_post_id, category_id) SELECT n.id, 2 FROM news_posts n WHERE n.slug='salary-negotiation-tactics-for-mid-career-professionals' AND NOT EXISTS (SELECT 1 FROM news_category WHERE news_post_id=n.id AND category_id=2);
INSERT INTO news_category (news_post_id, category_id) SELECT n.id, 2 FROM news_posts n WHERE n.slug='building-a-personal-brand-on-linkedin-guide-for-alumni' AND NOT EXISTS (SELECT 1 FROM news_category WHERE news_post_id=n.id AND category_id=2);
INSERT INTO news_category (news_post_id, category_id) SELECT n.id, 3 FROM news_posts n WHERE n.slug='remote-work-trends-reshaping-african-offices' AND NOT EXISTS (SELECT 1 FROM news_category WHERE news_post_id=n.id AND category_id=3);
INSERT INTO news_category (news_post_id, category_id) SELECT n.id, 3 FROM news_posts n WHERE n.slug='esg-investing-what-african-businesses-need-to-know' AND NOT EXISTS (SELECT 1 FROM news_category WHERE news_post_id=n.id AND category_id=3);
INSERT INTO news_category (news_post_id, category_id) SELECT n.id, 4 FROM news_posts n WHERE n.slug='rbsn-welcomes-new-dean-of-executive-education' AND NOT EXISTS (SELECT 1 FROM news_category WHERE news_post_id=n.id AND category_id=4);
INSERT INTO news_category (news_post_id, category_id) SELECT n.id, 4 FROM news_posts n WHERE n.slug='class-of-2024-graduation-ceremony-highlights' AND NOT EXISTS (SELECT 1 FROM news_category WHERE news_post_id=n.id AND category_id=4);
INSERT INTO news_category (news_post_id, category_id) SELECT n.id, 1 FROM news_posts n WHERE n.slug='from-banking-to-building-ibrahim-suleimans-career-pivot' AND NOT EXISTS (SELECT 1 FROM news_category WHERE news_post_id=n.id AND category_id=1);
INSERT INTO news_category (news_post_id, category_id) SELECT n.id, 2 FROM news_posts n WHERE n.slug='7-networking-habits-of-highly-successful-alumni' AND NOT EXISTS (SELECT 1 FROM news_category WHERE news_post_id=n.id AND category_id=2);
INSERT INTO news_category (news_post_id, category_id) SELECT n.id, 3 FROM news_posts n WHERE n.slug='the-rise-of-b2b-saas-in-west-africa' AND NOT EXISTS (SELECT 1 FROM news_category WHERE news_post_id=n.id AND category_id=3);
INSERT INTO news_category (news_post_id, category_id) SELECT n.id, 4 FROM news_posts n WHERE n.slug='rbsn-partners-with-local-startups-for-innovation-lab' AND NOT EXISTS (SELECT 1 FROM news_category WHERE news_post_id=n.id AND category_id=4);
INSERT INTO news_category (news_post_id, category_id) SELECT n.id, 2 FROM news_posts n WHERE n.slug='navigating-your-first-90-days-in-a-new-leadership-role' AND NOT EXISTS (SELECT 1 FROM news_category WHERE news_post_id=n.id AND category_id=2);
INSERT INTO news_category (news_post_id, category_id) SELECT n.id, 3 FROM news_posts n WHERE n.slug='women-leading-africas-energy-transition' AND NOT EXISTS (SELECT 1 FROM news_category WHERE news_post_id=n.id AND category_id=3);
INSERT INTO news_category (news_post_id, category_id) SELECT n.id, 1 FROM news_posts n WHERE n.slug='alumni-spotlight-chinwe-okafor-on-building-a-consultancy' AND NOT EXISTS (SELECT 1 FROM news_category WHERE news_post_id=n.id AND category_id=1);
INSERT INTO news_category (news_post_id, category_id) SELECT n.id, 3 FROM news_posts n WHERE n.slug='digital-payments-adoption-accelerates-across-nigeria' AND NOT EXISTS (SELECT 1 FROM news_category WHERE news_post_id=n.id AND category_id=3);
INSERT INTO news_category (news_post_id, category_id) SELECT n.id, 4 FROM news_posts n WHERE n.slug='rbsn-ranked-among-top-business-schools-in-africa' AND NOT EXISTS (SELECT 1 FROM news_category WHERE news_post_id=n.id AND category_id=4);
INSERT INTO news_category (news_post_id, category_id) SELECT n.id, 2 FROM news_posts n WHERE n.slug='how-to-ace-a-case-interview-tips-from-rbsn-alumni' AND NOT EXISTS (SELECT 1 FROM news_category WHERE news_post_id=n.id AND category_id=2);
INSERT INTO news_category (news_post_id, category_id) SELECT n.id, 3 FROM news_posts n WHERE n.slug='the-future-of-work-skills-employers-want-in-2026' AND NOT EXISTS (SELECT 1 FROM news_category WHERE news_post_id=n.id AND category_id=3);
INSERT INTO news_category (news_post_id, category_id) SELECT n.id, 4 FROM news_posts n WHERE n.slug='alumni-giving-day-raises-record-funds-for-scholarships' AND NOT EXISTS (SELECT 1 FROM news_category WHERE news_post_id=n.id AND category_id=4);

-- ---------------------------------------------------------------------
-- 8 additional career resources
-- ---------------------------------------------------------------------
INSERT INTO resources (title, description, link, category) SELECT 'Salary Benchmarking Report 2026','Compare compensation data across industries and seniority levels in West Africa.','#','Career' WHERE NOT EXISTS (SELECT 1 FROM resources WHERE title='Salary Benchmarking Report 2026');
INSERT INTO resources (title, description, link, category) SELECT 'Case Interview Prep Pack','Practice cases and frameworks used by top consulting firms.','#','Career' WHERE NOT EXISTS (SELECT 1 FROM resources WHERE title='Case Interview Prep Pack');
INSERT INTO resources (title, description, link, category) SELECT 'Pitch Deck Template for Founders','A clean, investor-ready pitch deck template used by alumni-founded startups.','#','Business' WHERE NOT EXISTS (SELECT 1 FROM resources WHERE title='Pitch Deck Template for Founders');
INSERT INTO resources (title, description, link, category) SELECT 'Financial Modelling Toolkit','Excel templates for DCF, LBO and three-statement modelling.','#','Finance' WHERE NOT EXISTS (SELECT 1 FROM resources WHERE title='Financial Modelling Toolkit');
INSERT INTO resources (title, description, link, category) SELECT 'Remote Work Productivity Guide','Best practices for staying productive and visible in distributed teams.','#','Career' WHERE NOT EXISTS (SELECT 1 FROM resources WHERE title='Remote Work Productivity Guide');
INSERT INTO resources (title, description, link, category) SELECT 'Negotiation Playbook','Scripts and tactics for negotiating offers, raises and vendor contracts.','#','Career' WHERE NOT EXISTS (SELECT 1 FROM resources WHERE title='Negotiation Playbook');
INSERT INTO resources (title, description, link, category) SELECT 'Public Speaking Masterclass Recording','A recorded masterclass on executive presence and public speaking.','#','Leadership' WHERE NOT EXISTS (SELECT 1 FROM resources WHERE title='Public Speaking Masterclass Recording');
INSERT INTO resources (title, description, link, category) SELECT 'Grant Writing Guide for Social Ventures','A guide to writing competitive grant applications for impact-driven ventures.','#','Business' WHERE NOT EXISTS (SELECT 1 FROM resources WHERE title='Grant Writing Guide for Social Ventures');

-- ---------------------------------------------------------------------
-- 8 additional alumni benefits
-- ---------------------------------------------------------------------
INSERT INTO benefits (title, description, category, link) SELECT 'Alumni Health Insurance Discount','Preferential group health insurance rates for RBSN alumni and their families.','Perks','#' WHERE NOT EXISTS (SELECT 1 FROM benefits WHERE title='Alumni Health Insurance Discount');
INSERT INTO benefits (title, description, category, link) SELECT 'Co-working Space Access','Discounted day passes at partner co-working spaces in Lagos, Abuja and Accra.','Perks','#' WHERE NOT EXISTS (SELECT 1 FROM benefits WHERE title='Co-working Space Access');
INSERT INTO benefits (title, description, category, link) SELECT 'Alumni Travel Discounts','Discounted rates with partner airlines and hotels for alumni travel.','Perks','#' WHERE NOT EXISTS (SELECT 1 FROM benefits WHERE title='Alumni Travel Discounts');
INSERT INTO benefits (title, description, category, link) SELECT 'Continuing Education Credit','Annual credit toward RBSN short courses and executive certificates.','Education','#' WHERE NOT EXISTS (SELECT 1 FROM benefits WHERE title='Continuing Education Credit');
INSERT INTO benefits (title, description, category, link) SELECT 'Alumni Legal Clinic','Free initial consultations with partner law firms for business and employment matters.','Career','#' WHERE NOT EXISTS (SELECT 1 FROM benefits WHERE title='Alumni Legal Clinic');
INSERT INTO benefits (title, description, category, link) SELECT 'Library & Journal Database Access','Lifetime access to RBSN''s digital library and academic journal subscriptions.','Academic','#' WHERE NOT EXISTS (SELECT 1 FROM benefits WHERE title='Library & Journal Database Access');
INSERT INTO benefits (title, description, category, link) SELECT 'Startup Incubator Priority Review','Fast-tracked application review for alumni-founded startups applying to the RBSN incubator.','Business','#' WHERE NOT EXISTS (SELECT 1 FROM benefits WHERE title='Startup Incubator Priority Review');
INSERT INTO benefits (title, description, category, link) SELECT 'Executive Coaching Sessions','Two complimentary executive coaching sessions per year for alumni in leadership roles.','Career','#' WHERE NOT EXISTS (SELECT 1 FROM benefits WHERE title='Executive Coaching Sessions');

-- ---------------------------------------------------------------------
-- 8 additional alumni-owned businesses
-- ---------------------------------------------------------------------
INSERT INTO businesses (owner_id, name, category, description, website, location)
SELECT (SELECT id FROM users WHERE email='segun.afolabi@example.com'),'Afolabi Logistics','Logistics','Last-mile delivery and fleet management services across 40+ Nigerian cities.','https://afolabilogistics.example.com','Ibadan, Nigeria' WHERE NOT EXISTS (SELECT 1 FROM businesses WHERE name='Afolabi Logistics');
INSERT INTO businesses (owner_id, name, category, description, website, location)
SELECT (SELECT id FROM users WHERE email='chinwe.okafor@example.com'),'Okafor & Co.','Consulting','Boutique management consultancy serving mid-market African companies.','https://okaforandco.example.com','Enugu, Nigeria' WHERE NOT EXISTS (SELECT 1 FROM businesses WHERE name='Okafor & Co.');
INSERT INTO businesses (owner_id, name, category, description, website, location)
SELECT (SELECT id FROM users WHERE email='ngozi.adeyemi@example.com'),'Adeyemi Fintech Advisory','Technology','Advisory services for early-stage fintech startups navigating regulation and fundraising.','https://adeyemifintech.example.com','Lagos, Nigeria' WHERE NOT EXISTS (SELECT 1 FROM businesses WHERE name='Adeyemi Fintech Advisory');
INSERT INTO businesses (owner_id, name, category, description, website, location)
SELECT (SELECT id FROM users WHERE email='folake.ogundimu@example.com'),'Ogundimu Brand Studio','Marketing','Brand strategy and creative studio serving consumer goods companies.','https://ogundimubrand.example.com','Lagos, Nigeria' WHERE NOT EXISTS (SELECT 1 FROM businesses WHERE name='Ogundimu Brand Studio');
INSERT INTO businesses (owner_id, name, category, description, website, location)
SELECT (SELECT id FROM users WHERE email='obinna.eze@example.com'),'Eze Capital Partners','Finance','Boutique corporate finance advisory for SMEs seeking growth capital.','https://ezecapital.example.com','Lagos, Nigeria' WHERE NOT EXISTS (SELECT 1 FROM businesses WHERE name='Eze Capital Partners');
INSERT INTO businesses (owner_id, name, category, description, website, location)
SELECT (SELECT id FROM users WHERE email='ronke.bamidele@example.com'),'Bamidele Health Consulting','Healthcare','Advisory services for health systems strengthening projects across West Africa.','https://bamideleconsulting.example.com','Accra, Ghana' WHERE NOT EXISTS (SELECT 1 FROM businesses WHERE name='Bamidele Health Consulting');
INSERT INTO businesses (owner_id, name, category, description, website, location)
SELECT (SELECT id FROM users WHERE email='uche.iwu@example.com'),'Iwu Real Estate Partners','Real Estate','Real estate investment advisory and market research for Sub-Saharan Africa.','https://iwurealestate.example.com','Lagos, Nigeria' WHERE NOT EXISTS (SELECT 1 FROM businesses WHERE name='Iwu Real Estate Partners');
INSERT INTO businesses (owner_id, name, category, description, website, location)
SELECT (SELECT id FROM users WHERE email='grace.effiong@example.com'),'Effiong Media Group','Media','Content production and media strategy studio for African brands.','https://effiongmedia.example.com','Lagos, Nigeria' WHERE NOT EXISTS (SELECT 1 FROM businesses WHERE name='Effiong Media Group');

-- ---------------------------------------------------------------------
-- 8 additional knowledge hub articles
-- ---------------------------------------------------------------------
INSERT INTO articles (user_id, title, category, body, status, published_at)
SELECT (SELECT id FROM users WHERE email='emeka.nwosu@example.com'),'What Africa''s M&A Boom Means for Mid-Career Professionals','article','<p>Deal activity across African markets has accelerated in recent years, creating new career pathways for professionals in corporate finance and strategy.</p><p>This piece breaks down what the trend means for anyone considering a move into investment banking or corporate development.</p>','published','2026-07-20 09:00:00' WHERE NOT EXISTS (SELECT 1 FROM articles WHERE title='What Africa''s M&A Boom Means for Mid-Career Professionals');

INSERT INTO articles (user_id, title, category, body, status, published_at)
SELECT (SELECT id FROM users WHERE email='chiamaka.umeh@example.com'),'Applying Machine Learning to Talent Matching: A Case Study','case_study','<p>This case study walks through how a data science team approached a talent-matching problem at scale, from feature engineering to model evaluation.</p>','published','2026-07-25 09:00:00' WHERE NOT EXISTS (SELECT 1 FROM articles WHERE title='Applying Machine Learning to Talent Matching: A Case Study');

INSERT INTO articles (user_id, title, category, body, status, published_at)
SELECT (SELECT id FROM users WHERE email='ibrahim.suleiman@example.com'),'Lean Six Sigma in African Manufacturing: A Research Note','research','<p>A research note examining the adoption and impact of Lean Six Sigma methodologies across African manufacturing operations.</p>','published','2026-07-28 09:00:00' WHERE NOT EXISTS (SELECT 1 FROM articles WHERE title='Lean Six Sigma in African Manufacturing: A Research Note');

INSERT INTO articles (user_id, title, category, body, status, published_at)
SELECT (SELECT id FROM users WHERE email='halima.bello@example.com'),'A Framework for Public Sector Advisory Engagements','white_paper','<p>This white paper outlines a practical framework for structuring public sector advisory engagements, drawn from projects across Northern Nigeria.</p>','published','2026-08-01 09:00:00' WHERE NOT EXISTS (SELECT 1 FROM articles WHERE title='A Framework for Public Sector Advisory Engagements');

INSERT INTO articles (user_id, title, category, body, status)
SELECT (SELECT id FROM users WHERE email='kemi.ajayi@example.com'),'Go-to-Market Lessons from Scaling Fintech Products','article','<p>Reflections on go-to-market strategy from launching merchant-facing fintech products across multiple African markets.</p>','pending' WHERE NOT EXISTS (SELECT 1 FROM articles WHERE title='Go-to-Market Lessons from Scaling Fintech Products');

INSERT INTO articles (user_id, title, category, body, status)
SELECT (SELECT id FROM users WHERE email='tobi.alabi@example.com'),'Marketplace Growth Tactics That Actually Work','article','<p>A rundown of growth tactics that moved the needle for a two-sided mobility marketplace, and a few that didn''t.</p>','pending' WHERE NOT EXISTS (SELECT 1 FROM articles WHERE title='Marketplace Growth Tactics That Actually Work');

INSERT INTO articles (user_id, title, category, body, status)
SELECT (SELECT id FROM users WHERE email='adaeze.chukwu@example.com'),'Navigating Regulatory Change in Nigerian Banking','research','<p>An overview of recent regulatory changes affecting retail banking products, and how compliance teams are adapting.</p>','pending' WHERE NOT EXISTS (SELECT 1 FROM articles WHERE title='Navigating Regulatory Change in Nigerian Banking');

INSERT INTO articles (user_id, title, category, body, status, published_at)
SELECT (SELECT id FROM users WHERE email='musa.aliyu@example.com'),'Safety Culture in High-Risk Industrial Environments','case_study','<p>A case study on building a safety-first culture across large-scale industrial operations in the Niger Delta.</p>','published','2026-08-05 09:00:00' WHERE NOT EXISTS (SELECT 1 FROM articles WHERE title='Safety Culture in High-Risk Industrial Environments');

-- ---------------------------------------------------------------------
-- 4 additional giving campaigns
-- ---------------------------------------------------------------------
INSERT INTO campaigns (title, description, goal_amount, raised_amount, status)
SELECT 'Women in Leadership Fund','Supporting scholarships and mentorship for women pursuing MBA and executive programmes.',15000000.00,6250000.00,'active' WHERE NOT EXISTS (SELECT 1 FROM campaigns WHERE title='Women in Leadership Fund');
INSERT INTO campaigns (title, description, goal_amount, raised_amount, status)
SELECT 'Innovation Lab Endowment','Funding a permanent innovation lab space for students and alumni founders.',25000000.00,9800000.00,'active' WHERE NOT EXISTS (SELECT 1 FROM campaigns WHERE title='Innovation Lab Endowment');
INSERT INTO campaigns (title, description, goal_amount, raised_amount, status)
SELECT 'Rural Business Outreach Programme','Bringing executive education to entrepreneurs in underserved regions.',8000000.00,8000000.00,'closed' WHERE NOT EXISTS (SELECT 1 FROM campaigns WHERE title='Rural Business Outreach Programme');
INSERT INTO campaigns (title, description, goal_amount, raised_amount, status)
SELECT 'Alumni Emergency Relief Fund','Short-term financial support for alumni facing unexpected hardship.',5000000.00,1750000.00,'active' WHERE NOT EXISTS (SELECT 1 FROM campaigns WHERE title='Alumni Emergency Relief Fund');

-- ---------------------------------------------------------------------
-- 10 additional community feed posts
-- ---------------------------------------------------------------------
INSERT INTO posts (user_id, post_type, content)
SELECT (SELECT id FROM users WHERE email='ngozi.adeyemi@example.com'),'achievement','Thrilled to share that our team at Flutterwave just crossed 1 million merchants on the platform! Grateful for the RBSN network that shaped my product thinking.' WHERE NOT EXISTS (SELECT 1 FROM posts WHERE user_id=(SELECT id FROM users WHERE email='ngozi.adeyemi@example.com') AND content LIKE 'Thrilled to share%');

INSERT INTO posts (user_id, post_type, content)
SELECT (SELECT id FROM users WHERE email='segun.afolabi@example.com'),'announcement','Afolabi Logistics is hiring! We just posted a Fleet Operations Manager role on the job board &mdash; would love to bring on a fellow alumnus.' WHERE NOT EXISTS (SELECT 1 FROM posts WHERE user_id=(SELECT id FROM users WHERE email='segun.afolabi@example.com') AND content LIKE 'Afolabi Logistics is hiring%');

INSERT INTO posts (user_id, post_type, content)
SELECT (SELECT id FROM users WHERE email='chinwe.okafor@example.com'),'question','Curious to hear from fellow alumni: what was the hardest part of going from corporate to running your own consultancy?' WHERE NOT EXISTS (SELECT 1 FROM posts WHERE user_id=(SELECT id FROM users WHERE email='chinwe.okafor@example.com') AND content LIKE 'Curious to hear%');

INSERT INTO posts (user_id, post_type, content)
SELECT (SELECT id FROM users WHERE email='emeka.nwosu@example.com'),'general','Great turnout at last week''s Investment Banking panel. Always energizing to see how many alumni are building careers in finance across the continent.' WHERE NOT EXISTS (SELECT 1 FROM posts WHERE user_id=(SELECT id FROM users WHERE email='emeka.nwosu@example.com') AND content LIKE 'Great turnout%');

INSERT INTO posts (user_id, post_type, content)
SELECT (SELECT id FROM users WHERE email='folake.ogundimu@example.com'),'achievement','Our latest campaign at Nestle Nigeria just won a regional marketing award. Proud of the team!' WHERE NOT EXISTS (SELECT 1 FROM posts WHERE user_id=(SELECT id FROM users WHERE email='folake.ogundimu@example.com') AND content LIKE 'Our latest campaign%');

INSERT INTO posts (user_id, post_type, content)
SELECT (SELECT id FROM users WHERE email='ibrahim.suleiman@example.com'),'general','Spent the weekend mentoring two recent graduates on breaking into operations roles. Always rewarding to give back to the network.' WHERE NOT EXISTS (SELECT 1 FROM posts WHERE user_id=(SELECT id FROM users WHERE email='ibrahim.suleiman@example.com') AND content LIKE 'Spent the weekend%');

INSERT INTO posts (user_id, post_type, content)
SELECT (SELECT id FROM users WHERE email='kemi.ajayi@example.com'),'event','Looking forward to the Fintech Founders Meetup next month &mdash; who else is attending?' WHERE NOT EXISTS (SELECT 1 FROM posts WHERE user_id=(SELECT id FROM users WHERE email='kemi.ajayi@example.com') AND content LIKE 'Looking forward to the Fintech%');

INSERT INTO posts (user_id, post_type, content)
SELECT (SELECT id FROM users WHERE email='obinna.eze@example.com'),'general','Reading recommendation for finance folks: just finished a great book on capital allocation. Happy to share the title if anyone''s interested.' WHERE NOT EXISTS (SELECT 1 FROM posts WHERE user_id=(SELECT id FROM users WHERE email='obinna.eze@example.com') AND content LIKE 'Reading recommendation%');

INSERT INTO posts (user_id, post_type, content)
SELECT (SELECT id FROM users WHERE email='tobi.alabi@example.com'),'achievement','Hit a major growth milestone on the rider acquisition team this quarter. Small consistent experiments really do compound.' WHERE NOT EXISTS (SELECT 1 FROM posts WHERE user_id=(SELECT id FROM users WHERE email='tobi.alabi@example.com') AND content LIKE 'Hit a major growth milestone%');

INSERT INTO posts (user_id, post_type, content)
SELECT (SELECT id FROM users WHERE email='grace.effiong@example.com'),'partnership','Excited to announce a new content partnership between MultiChoice and a group of local creators &mdash; more details soon!' WHERE NOT EXISTS (SELECT 1 FROM posts WHERE user_id=(SELECT id FROM users WHERE email='grace.effiong@example.com') AND content LIKE 'Excited to announce a new content%');

-- ---------------------------------------------------------------------
-- 13 additional knowledge hub articles (rounds the Knowledge library out
-- to 20+, spanning all four categories and the Trending Topics pills)
-- ---------------------------------------------------------------------
INSERT INTO articles (user_id, title, category, body, status, published_at)
SELECT (SELECT id FROM users WHERE email='chiamaka.umeh@example.com'),'The AI Playbook for Traditional Nigerian Businesses','article','<p>Artificial intelligence is no longer just a Silicon Valley conversation. This playbook walks traditional Nigerian businesses through practical, low-cost entry points for AI adoption &mdash; from customer service automation to demand forecasting.</p><p>The biggest barrier isn''t technology, it''s data hygiene. Start there.</p>','published','2026-08-06 09:00:00' WHERE NOT EXISTS (SELECT 1 FROM articles WHERE title='The AI Playbook for Traditional Nigerian Businesses');

INSERT INTO articles (user_id, title, category, body, status, published_at)
SELECT (SELECT id FROM users WHERE email='ibrahim.suleiman@example.com'),'Sustainability Reporting: A Primer for African Manufacturers','research','<p>As global buyers tighten ESG requirements, African manufacturers are under growing pressure to formalize sustainability reporting. This research note outlines the frameworks gaining the most traction across the continent.</p>','published','2026-08-08 09:00:00' WHERE NOT EXISTS (SELECT 1 FROM articles WHERE title='Sustainability Reporting: A Primer for African Manufacturers');

INSERT INTO articles (user_id, title, category, body, status, published_at)
SELECT (SELECT id FROM users WHERE email='folake.ogundimu@example.com'),'Scaling Marketing Teams Without Losing Brand Voice','article','<p>Growing a marketing team from three people to thirty introduces real risk to brand consistency. Here''s the operating model we used to scale without diluting the brand.</p>','published','2026-08-10 09:00:00' WHERE NOT EXISTS (SELECT 1 FROM articles WHERE title='Scaling Marketing Teams Without Losing Brand Voice');

INSERT INTO articles (user_id, title, category, body, status, published_at)
SELECT (SELECT id FROM users WHERE email='segun.afolabi@example.com'),'Operations Playbook: Lessons from a 200-Vehicle Fleet','case_study','<p>Scaling a delivery fleet from two vans to two hundred vehicles surfaces operational challenges most playbooks don''t cover. This case study walks through routing, maintenance, and driver retention lessons learned the hard way.</p>','published','2026-08-12 09:00:00' WHERE NOT EXISTS (SELECT 1 FROM articles WHERE title='Operations Playbook: Lessons from a 200-Vehicle Fleet');

INSERT INTO articles (user_id, title, category, body, status, published_at)
SELECT (SELECT id FROM users WHERE email='kemi.ajayi@example.com'),'Innovation Labs: Do They Actually Work?','white_paper','<p>Corporate innovation labs promise breakthrough thinking but often produce little beyond a nicely designed office. This white paper examines what separates innovation labs that ship real products from those that don''t.</p>','published','2026-08-13 09:00:00' WHERE NOT EXISTS (SELECT 1 FROM articles WHERE title='Innovation Labs: Do They Actually Work?');

INSERT INTO articles (user_id, title, category, body, status, published_at)
SELECT (SELECT id FROM users WHERE email='chinwe.okafor@example.com'),'Entrepreneurship in Emerging Markets: A Founder''s Field Guide','article','<p>Building a company in an emerging market means solving problems most Western playbooks don''t anticipate &mdash; from currency volatility to infrastructure gaps. This field guide draws on a decade of consulting to founders across the continent.</p>','published','2026-08-14 09:00:00' WHERE NOT EXISTS (SELECT 1 FROM articles WHERE title='Entrepreneurship in Emerging Markets: A Founder''s Field Guide');

INSERT INTO articles (user_id, title, category, body, status, published_at)
SELECT (SELECT id FROM users WHERE email='emeka.nwosu@example.com'),'Leadership Lessons from a Decade in Banking','article','<p>A decade spent moving from analyst to VP in investment banking teaches lessons no MBA case study fully captures. Here are the ones that mattered most.</p>','published','2026-08-15 09:00:00' WHERE NOT EXISTS (SELECT 1 FROM articles WHERE title='Leadership Lessons from a Decade in Banking');

INSERT INTO articles (user_id, title, category, body, status)
SELECT (SELECT id FROM users WHERE email='kemi.ajayi@example.com'),'Strategic Pricing for African SaaS Startups','research','<p>Pricing strategy is one of the most under-invested areas for early-stage African SaaS companies. This research note proposes a framework for pricing in markets with wide income variance.</p>','pending' WHERE NOT EXISTS (SELECT 1 FROM articles WHERE title='Strategic Pricing for African SaaS Startups');

INSERT INTO articles (user_id, title, category, body, status, published_at)
SELECT (SELECT id FROM users WHERE email='adaeze.chukwu@example.com'),'AI Adoption Risks Boards Should Understand','white_paper','<p>As companies rush to adopt AI tools, boards are often left without a clear risk framework. This white paper outlines the governance questions every board should be asking.</p>','published','2026-08-16 09:00:00' WHERE NOT EXISTS (SELECT 1 FROM articles WHERE title='AI Adoption Risks Boards Should Understand');

INSERT INTO articles (user_id, title, category, body, status, published_at)
SELECT (SELECT id FROM users WHERE email='obinna.eze@example.com'),'Building Financially Resilient SMEs','article','<p>Most SME failures trace back to cash flow mismanagement, not lack of demand. This article breaks down the financial discipline practices that separate resilient SMEs from the rest.</p>','published','2026-08-17 09:00:00' WHERE NOT EXISTS (SELECT 1 FROM articles WHERE title='Building Financially Resilient SMEs');

INSERT INTO articles (user_id, title, category, body, status, published_at)
SELECT (SELECT id FROM users WHERE email='ronke.bamidele@example.com'),'Change Management During Mergers: A Case Study','case_study','<p>Mergers succeed or fail based on how well the people side of change is managed. This case study examines a health-sector merger and the change management practices that kept staff engaged through the transition.</p>','published','2026-08-18 09:00:00' WHERE NOT EXISTS (SELECT 1 FROM articles WHERE title='Change Management During Mergers: A Case Study');

INSERT INTO articles (user_id, title, category, body, status)
SELECT (SELECT id FROM users WHERE email='blessing.etim@example.com'),'Employer Branding in a Competitive Talent Market','article','<p>As competition for skilled talent intensifies across telecom and tech, employer branding has become a genuine competitive advantage. Here''s how we approached it.</p>','pending' WHERE NOT EXISTS (SELECT 1 FROM articles WHERE title='Employer Branding in a Competitive Talent Market');

INSERT INTO articles (user_id, title, category, body, status, published_at)
SELECT (SELECT id FROM users WHERE email='segun.afolabi@example.com'),'The Economics of Last-Mile Delivery in Africa','research','<p>Last-mile delivery costs in African cities differ substantially from Western benchmarks due to infrastructure and population density factors. This research note quantifies the gap and what it means for logistics business models.</p>','published','2026-08-19 09:00:00' WHERE NOT EXISTS (SELECT 1 FROM articles WHERE title='The Economics of Last-Mile Delivery in Africa');
