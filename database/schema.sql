-- Alumni Portal database schema + seed data
--
-- This file does NOT create or select a database - run it against a database
-- that already exists (most hosts, including Hostinger's managed MySQL, don't
-- grant the app's DB user CREATE DATABASE privileges).
--
-- Local XAMPP (creates the DB first, since the root user can):
--   C:\xampp\mysql\bin\mysql.exe -u root -e "CREATE DATABASE IF NOT EXISTS alumni_portal CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
--   C:\xampp\mysql\bin\mysql.exe -u root alumni_portal < database/schema.sql
--
-- Hostinger / shared hosting (phpMyAdmin): create the database in hPanel first,
-- then in phpMyAdmin select that database from the left sidebar before using
-- Import - the tables below will be created inside whichever database is
-- currently selected.

-- ---------------------------------------------------------------------
-- users  (single table for both admin and alumni accounts)
-- ---------------------------------------------------------------------
CREATE TABLE users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  role ENUM('admin','editor','alumni') NOT NULL DEFAULT 'alumni',
  name VARCHAR(150) NOT NULL,
  preferred_name VARCHAR(100) NULL,
  email VARCHAR(150) NOT NULL UNIQUE,
  password_hash VARCHAR(255) NOT NULL,
  avatar VARCHAR(255) NULL,
  headline VARCHAR(200) NULL,
  company VARCHAR(150) NULL,
  company_size VARCHAR(50) NULL,
  years_in_role VARCHAR(50) NULL,
  industry VARCHAR(150) NULL,
  city VARCHAR(100) NULL,
  country VARCHAR(100) NULL,
  graduation_year YEAR NULL,
  program VARCHAR(150) NULL,
  matric_number VARCHAR(50) NULL UNIQUE,
  cohort VARCHAR(150) NULL,
  bio TEXT NULL,
  skills TEXT NULL,
  expertise_areas TEXT NULL,
  business_interests TEXT NULL,
  personal_interests TEXT NULL,
  linkedin_url VARCHAR(255) NULL,
  personal_website VARCHAR(255) NULL,
  twitter_url VARCHAR(255) NULL,
  phone VARCHAR(50) NULL,
  status ENUM('active','suspended') NOT NULL DEFAULT 'active',
  profile_visibility ENUM('public','alumni','private') NOT NULL DEFAULT 'public',
  show_email TINYINT(1) NOT NULL DEFAULT 0,
  show_phone TINYINT(1) NOT NULL DEFAULT 0,
  is_mentor TINYINT(1) NOT NULL DEFAULT 0,
  mentorship_areas TEXT NULL,
  mentor_availability ENUM('open','limited','closed') NOT NULL DEFAULT 'open',
  is_spotlighted TINYINT(1) NOT NULL DEFAULT 0,
  spotlight_note TEXT NULL,
  resume_url VARCHAR(255) NULL,
  resume_file VARCHAR(255) NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  last_active_at TIMESTAMP NULL
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- alumni_roster  (admin-entered pre-approved records — a matric number,
-- graduation year and cohort must match one of these rows before
-- registration is allowed, and each row can only be claimed once)
-- ---------------------------------------------------------------------
CREATE TABLE alumni_roster (
  id INT AUTO_INCREMENT PRIMARY KEY,
  matric_number VARCHAR(50) NOT NULL UNIQUE,
  full_name VARCHAR(150) NULL,
  graduation_year INT NOT NULL,
  cohort VARCHAR(150) NOT NULL,
  claimed_by_user_id INT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  CONSTRAINT fk_alumni_roster_user FOREIGN KEY (claimed_by_user_id) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- user_education / user_experience  (repeatable history entries shown
-- on the Edit Profile "Education" and "Experience" tabs)
-- ---------------------------------------------------------------------
CREATE TABLE user_education (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  school VARCHAR(150) NOT NULL,
  degree VARCHAR(150) NULL,
  field_of_study VARCHAR(150) NULL,
  start_year YEAR NULL,
  end_year YEAR NULL,
  sort_order INT NOT NULL DEFAULT 0,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_user_education_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE user_experience (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  title VARCHAR(150) NOT NULL,
  company VARCHAR(150) NOT NULL,
  location VARCHAR(150) NULL,
  start_date DATE NULL,
  end_date DATE NULL,
  is_current TINYINT(1) NOT NULL DEFAULT 0,
  description TEXT NULL,
  sort_order INT NOT NULL DEFAULT 0,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_user_experience_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- connections / follows  (alumni-to-alumni networking)
-- ---------------------------------------------------------------------
CREATE TABLE connections (
  id INT AUTO_INCREMENT PRIMARY KEY,
  requester_id INT NOT NULL,
  recipient_id INT NOT NULL,
  status ENUM('pending','accepted') NOT NULL DEFAULT 'pending',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  responded_at TIMESTAMP NULL,
  FOREIGN KEY (requester_id) REFERENCES users(id) ON DELETE CASCADE,
  FOREIGN KEY (recipient_id) REFERENCES users(id) ON DELETE CASCADE,
  UNIQUE KEY uniq_pair (requester_id, recipient_id)
) ENGINE=InnoDB;

CREATE TABLE follows (
  follower_id INT NOT NULL,
  followed_id INT NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (follower_id, followed_id),
  FOREIGN KEY (follower_id) REFERENCES users(id) ON DELETE CASCADE,
  FOREIGN KEY (followed_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- posts / post_likes / post_comments  (alumni community feed)
-- ---------------------------------------------------------------------
CREATE TABLE posts (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  post_type ENUM('general','achievement','announcement','question','job_opportunity','event','partnership') NOT NULL DEFAULT 'general',
  status ENUM('draft','published') NOT NULL DEFAULT 'published',
  visibility ENUM('public','connections') NOT NULL DEFAULT 'public',
  content TEXT NOT NULL,
  image VARCHAR(255) NULL,
  shared_post_id INT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
  FOREIGN KEY (shared_post_id) REFERENCES posts(id) ON DELETE SET NULL
) ENGINE=InnoDB;

CREATE TABLE post_likes (
  post_id INT NOT NULL,
  user_id INT NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (post_id, user_id),
  FOREIGN KEY (post_id) REFERENCES posts(id) ON DELETE CASCADE,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE post_comments (
  id INT AUTO_INCREMENT PRIMARY KEY,
  post_id INT NOT NULL,
  user_id INT NOT NULL,
  content TEXT NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (post_id) REFERENCES posts(id) ON DELETE CASCADE,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- news_posts
-- ---------------------------------------------------------------------
CREATE TABLE news_posts (
  id INT AUTO_INCREMENT PRIMARY KEY,
  title VARCHAR(255) NOT NULL,
  slug VARCHAR(255) NOT NULL UNIQUE,
  excerpt VARCHAR(500) NULL,
  body MEDIUMTEXT NULL,
  image VARCHAR(500) NULL,
  author VARCHAR(150) NULL,
  region VARCHAR(100) NULL,
  tags VARCHAR(500) NULL,
  status ENUM('draft','published') NOT NULL DEFAULT 'published',
  published_at DATETIME NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- companies  (reusable company profiles for job listings)
-- ---------------------------------------------------------------------
CREATE TABLE companies (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(150) NOT NULL,
  industry VARCHAR(100) NULL,
  logo VARCHAR(255) NULL,
  about MEDIUMTEXT NULL,
  website VARCHAR(255) NULL,
  location VARCHAR(150) NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- categories  (shared table, scoped per content type via `type`)
-- ---------------------------------------------------------------------
CREATE TABLE categories (
  id INT AUTO_INCREMENT PRIMARY KEY,
  type ENUM('job','news') NOT NULL,
  name VARCHAR(100) NOT NULL,
  slug VARCHAR(100) NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY uniq_type_slug (type, slug)
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- jobs
-- ---------------------------------------------------------------------
CREATE TABLE jobs (
  id INT AUTO_INCREMENT PRIMARY KEY,
  posted_by INT NULL COMMENT 'alumni user who self-posted this job; NULL = posted by admin',
  title VARCHAR(200) NOT NULL,
  company VARCHAR(150) NOT NULL,
  company_id INT NULL,
  company_logo VARCHAR(255) NULL,
  logo_display_mode ENUM('logo','badge') NOT NULL DEFAULT 'logo',
  company_bg_color VARCHAR(20) NOT NULL DEFAULT '#f8fafc',
  company_text_color VARCHAR(20) NOT NULL DEFAULT '#091a2e',
  location VARCHAR(150) NULL,
  job_type VARCHAR(50) NOT NULL DEFAULT 'Full-time',
  experience_level ENUM('entry','mid','senior','executive') NULL,
  work_mode ENUM('onsite','remote','hybrid') NOT NULL DEFAULT 'onsite',
  salary_min INT NULL,
  salary_max INT NULL,
  description MEDIUMTEXT NULL,
  responsibilities MEDIUMTEXT NULL,
  requirements MEDIUMTEXT NULL,
  company_about MEDIUMTEXT NULL,
  apply_type ENUM('link','email') NOT NULL DEFAULT 'email',
  apply_value VARCHAR(255) NULL,
  is_featured TINYINT(1) NOT NULL DEFAULT 0,
  status ENUM('open','closed') NOT NULL DEFAULT 'open',
  approval_status ENUM('approved','pending','rejected','draft') NOT NULL DEFAULT 'approved',
  posted_at DATETIME NULL,
  closing_date DATE NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  CONSTRAINT fk_jobs_company FOREIGN KEY (company_id) REFERENCES companies(id) ON DELETE SET NULL,
  CONSTRAINT fk_jobs_posted_by FOREIGN KEY (posted_by) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- job_category / news_category  (many-to-many pivot tables)
-- ---------------------------------------------------------------------
CREATE TABLE job_category (
  job_id INT NOT NULL,
  category_id INT NOT NULL,
  PRIMARY KEY (job_id, category_id),
  FOREIGN KEY (job_id) REFERENCES jobs(id) ON DELETE CASCADE,
  FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE news_category (
  news_post_id INT NOT NULL,
  category_id INT NOT NULL,
  PRIMARY KEY (news_post_id, category_id),
  FOREIGN KEY (news_post_id) REFERENCES news_posts(id) ON DELETE CASCADE,
  FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- events
-- ---------------------------------------------------------------------
CREATE TABLE events (
  id INT AUTO_INCREMENT PRIMARY KEY,
  title VARCHAR(200) NOT NULL,
  description TEXT NULL,
  image VARCHAR(255) NULL,
  location VARCHAR(150) NULL,
  category VARCHAR(100) NULL,
  is_virtual TINYINT(1) NOT NULL DEFAULT 0,
  is_featured TINYINT(1) NOT NULL DEFAULT 0,
  event_date DATE NOT NULL,
  event_time VARCHAR(50) NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE saved_events (
  user_id INT NOT NULL,
  event_id INT NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (user_id, event_id),
  CONSTRAINT fk_saved_events_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
  CONSTRAINT fk_saved_events_event FOREIGN KEY (event_id) REFERENCES events(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- resources
-- ---------------------------------------------------------------------
CREATE TABLE resources (
  id INT AUTO_INCREMENT PRIMARY KEY,
  title VARCHAR(200) NOT NULL,
  description VARCHAR(500) NULL,
  link VARCHAR(255) NULL,
  category VARCHAR(100) NULL,
  resource_type VARCHAR(50) NULL,
  read_time_minutes INT NULL,
  image VARCHAR(255) NULL,
  is_featured TINYINT(1) NOT NULL DEFAULT 0,
  difficulty ENUM('beginner','intermediate','advanced') NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- saved_resources  (per-user bookmarks on Career Resources)
-- ---------------------------------------------------------------------
CREATE TABLE saved_resources (
  user_id INT NOT NULL,
  resource_id INT NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (user_id, resource_id),
  CONSTRAINT fk_saved_resources_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
  CONSTRAINT fk_saved_resources_resource FOREIGN KEY (resource_id) REFERENCES resources(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- resume_reviews  (alumni submit a resume link; a mentor claims it and leaves feedback)
-- ---------------------------------------------------------------------
CREATE TABLE resume_reviews (
  id INT AUTO_INCREMENT PRIMARY KEY,
  requester_id INT NOT NULL,
  reviewer_id INT NULL,
  resume_url VARCHAR(255) NULL,
  resume_link VARCHAR(255) NULL,
  message TEXT NULL,
  status ENUM('pending','claimed','completed') NOT NULL DEFAULT 'pending',
  feedback TEXT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  claimed_at TIMESTAMP NULL,
  completed_at TIMESTAMP NULL,
  CONSTRAINT fk_resume_reviews_requester FOREIGN KEY (requester_id) REFERENCES users(id) ON DELETE CASCADE,
  CONSTRAINT fk_resume_reviews_reviewer FOREIGN KEY (reviewer_id) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- settings  (key/value store for homepage hero, CTA banner, metrics bar)
-- ---------------------------------------------------------------------
CREATE TABLE settings (
  setting_key VARCHAR(100) PRIMARY KEY,
  setting_value TEXT NULL
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- hero_slides  (homepage hero carousel, admin-managed, unlimited slides)
-- ---------------------------------------------------------------------
CREATE TABLE hero_slides (
  id INT AUTO_INCREMENT PRIMARY KEY,
  sort_order INT NOT NULL DEFAULT 0,
  enabled TINYINT(1) NOT NULL DEFAULT 1,
  image VARCHAR(500) NULL,
  title VARCHAR(200) NULL,
  highlight VARCHAR(200) NULL,
  subtitle VARCHAR(255) NULL,
  description TEXT NULL,
  content_align ENUM('left','center','right') NOT NULL DEFAULT 'left',
  btn1_text VARCHAR(100) NULL,
  btn1_show TINYINT(1) NOT NULL DEFAULT 1,
  btn1_link VARCHAR(255) NULL,
  btn2_text VARCHAR(100) NULL,
  btn2_show TINYINT(1) NOT NULL DEFAULT 1,
  btn2_link VARCHAR(255) NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- =======================================================================
-- Seed data
-- =======================================================================

-- Seeded admin account. Password: ChangeMe@123  (please change after first login)
INSERT INTO users (role, name, email, password_hash, status) VALUES
('admin', 'RBSN Admin', 'icebestlimited2@gmail.com', '$2y$10$j6SV/iJPfqeBdIGK0c0FRu28cIyo0LHSuTAD3BmqxPCsJxi0VkkLq', 'active');

-- Sample alumni (same seeded password: ChangeMe@123) so the directory isn't empty on first run.
INSERT INTO users (role, name, email, password_hash, headline, company, industry, city, country, graduation_year, program, bio, skills, expertise_areas, business_interests, status) VALUES
('alumni', 'Chidi Okonkwo', 'chidi.okonkwo@example.com', '$2y$10$j6SV/iJPfqeBdIGK0c0FRu28cIyo0LHSuTAD3BmqxPCsJxi0VkkLq', 'Founder & CEO', 'Okonkwo Ventures', 'Technology & Fintech', 'Lagos', 'Nigeria', 2019, 'Executive MBA', 'Chidi leveraged his Executive MBA to transform challenges into opportunities and build a thriving business across West Africa.', 'Product Strategy, Fundraising, Leadership', 'Entrepreneurship, Venture Building', 'Payments, SaaS', 'active'),
('alumni', 'Amaka Eze', 'amaka.eze@example.com', '$2y$10$j6SV/iJPfqeBdIGK0c0FRu28cIyo0LHSuTAD3BmqxPCsJxi0VkkLq', 'Strategy & Operations Manager', 'Deloitte West Africa', 'Professional Services', 'Lagos', 'Nigeria', 2021, 'MBA', 'Passionate about operational excellence and business transformation across emerging markets.', 'Operations, Process Improvement, Change Management', 'Corporate Strategy, Operations', 'Consulting, Manufacturing', 'active'),
('alumni', 'Tunde Bakare', 'tunde.bakare@example.com', '$2y$10$j6SV/iJPfqeBdIGK0c0FRu28cIyo0LHSuTAD3BmqxPCsJxi0VkkLq', 'Relationship Manager', 'Guaranty Trust Bank', 'Banking & Finance', 'Abuja', 'Nigeria', 2020, 'MSc Finance', 'Focused on building lasting client relationships and driving financial inclusion.', 'Relationship Management, Credit Analysis', 'Corporate Banking, Financial Inclusion', 'Fintech, SME Lending', 'active');

-- Sample community feed posts
INSERT INTO posts (user_id, post_type, content, created_at) VALUES
((SELECT id FROM users WHERE email='amaka.eze@example.com'), 'achievement', 'Excited to share that I have been promoted to Strategy & Operations Manager at Deloitte West Africa! Grateful for the mentorship and lessons from my time in the MBA programme. #Grateful #NewChapter', '2026-08-18 09:00:00'),
((SELECT id FROM users WHERE email='tunde.bakare@example.com'), 'announcement', 'Guaranty Trust Bank is opening a new innovation hub in Abuja focused on SME lending and fintech partnerships. Excited to be part of the founding team!', '2026-08-19 12:30:00'),
((SELECT id FROM users WHERE email='chidi.okonkwo@example.com'), 'question', 'Any fellow alumni with experience scaling a SaaS product across West African markets? Would love to compare notes on go-to-market strategy. Happy to grab a virtual coffee.', '2026-08-19 16:45:00'),
((SELECT id FROM users WHERE email='amaka.eze@example.com'), 'partnership', 'Congratulations to Chidi Okonkwo, Executive MBA 2019, on his venture Okonkwo Ventures closing a new partnership round to expand payment infrastructure across the region!', '2026-08-20 08:15:00');

-- News / Blog posts
INSERT INTO news_posts (title, slug, excerpt, body, image, author, status, published_at) VALUES
('From RBSN to Global Impact: The Journey of Chidi Okonkwo', 'from-rbsn-to-global-impact-chidi-okonkwo', 'Discover how Chidi leveraged his Executive MBA to transform challenges into opportunities and build a thriving business.', 'Discover how Chidi leveraged his Executive MBA to transform challenges into opportunities and build a thriving business. From his first year at Rome Business School Nigeria, Chidi set his sights on entrepreneurship, and today his venture spans multiple West African markets.', 'https://images.unsplash.com/photo-1486406146926-c627a92ad1ab?auto=format&fit=crop&w=900&q=80', 'RBSN Team', 'published', '2026-08-18 09:00:00'),
('5 Leadership Skills Every Manager Must Develop in 2024', '5-leadership-skills-every-manager-must-develop', 'Explore the essential leadership skills that set top-performing managers apart in a rapidly changing business world.', 'Explore the essential leadership skills that set top-performing managers apart in a rapidly changing business world: emotional intelligence, adaptive communication, decisive strategy, coaching mindset, and digital fluency.', 'https://images.unsplash.com/photo-1522071820081-009f0129c71c?auto=format&fit=crop&w=600&q=80', 'RBSN Team', 'published', '2026-08-14 09:00:00'),
('The Future of Business in Africa: Trends to Watch', 'future-of-business-in-africa-trends-to-watch', 'From fintech to green energy, here are the trends reshaping business across the continent.', 'From fintech to green energy, here are the trends reshaping business across the continent, and what they mean for the next generation of African business leaders.', 'https://images.unsplash.com/photo-1519389950473-47ba0277781c?auto=format&fit=crop&w=600&q=80', 'RBSN Team', 'published', '2026-08-10 09:00:00'),
('RBSN Hosts Successful Alumni Networking Dinner in Lagos', 'rbsn-hosts-alumni-networking-dinner-in-lagos', 'Alumni gathered in Lagos for an evening of connection, celebration, and new partnerships.', 'Alumni gathered in Lagos for an evening of connection, celebration, and new partnerships, marking another milestone in RBSN''s growing alumni community.', 'https://images.unsplash.com/photo-1511578314322-379afb476865?auto=format&fit=crop&w=600&q=80', 'RBSN Team', 'published', '2026-08-05 09:00:00');

-- Companies (reusable profiles, linked to the jobs below)
INSERT INTO companies (name, about, website, location) VALUES
('KPMG Nigeria', 'KPMG Nigeria is a leading professional services firm providing audit, tax, and advisory services across West Africa, helping clients navigate complex business challenges.', 'https://home.kpmg/ng', 'Lagos, Nigeria'),
('Deloitte West Africa', 'Deloitte West Africa delivers audit, consulting, financial advisory, risk advisory, and tax services to public and private clients across multiple industries.', 'https://www2.deloitte.com/ng', 'Lagos, Nigeria'),
('Guaranty Trust Bank', 'Guaranty Trust Bank (GTBank) is one of Africa''s leading financial institutions, committed to delivering exceptional banking services and driving financial inclusion.', 'https://gtbank.com', 'Abuja, Nigeria');

-- Categories
INSERT INTO categories (type, name, slug) VALUES
('news', 'Alumni Stories', 'alumni-stories'),
('news', 'Career Tips', 'career-tips'),
('news', 'Industry Insights', 'industry-insights'),
('news', 'Campus News', 'campus-news'),
('job', 'Consulting', 'consulting'),
('job', 'Banking & Finance', 'banking-finance'),
('job', 'Technology', 'technology'),
('job', 'Marketing', 'marketing'),
('job', 'Operations', 'operations');

INSERT INTO news_category (news_post_id, category_id)
SELECT n.id, c.id FROM news_posts n JOIN categories c ON c.type='news' AND (
  (n.slug='from-rbsn-to-global-impact-chidi-okonkwo' AND c.slug='alumni-stories') OR
  (n.slug='5-leadership-skills-every-manager-must-develop' AND c.slug='career-tips') OR
  (n.slug='future-of-business-in-africa-trends-to-watch' AND c.slug='industry-insights') OR
  (n.slug='rbsn-hosts-alumni-networking-dinner-in-lagos' AND c.slug='campus-news')
);

-- Jobs
INSERT INTO jobs (title, company, company_id, company_bg_color, company_text_color, location, job_type, description, company_about, apply_type, apply_value, is_featured, status, posted_at) VALUES
('Senior Business Consultant', 'KPMG Nigeria', (SELECT id FROM companies WHERE name='KPMG Nigeria'), '#f8fafc', '#00338D', 'Lagos, Nigeria', 'Full-time', 'Lead client engagements across strategy, operations, and digital transformation for KPMG Nigeria''s consulting practice.', (SELECT about FROM companies WHERE name='KPMG Nigeria'), 'email', 'careers@rbsn.example.com', 1, 'open', '2026-08-18 09:00:00'),
('Strategy & Operations Manager', 'Deloitte West Africa', (SELECT id FROM companies WHERE name='Deloitte West Africa'), '#f8fafc', '#86BC25', 'Lagos, Nigeria', 'Full-time', 'Drive strategic planning and operational improvement initiatives for leading clients across West Africa.', (SELECT about FROM companies WHERE name='Deloitte West Africa'), 'email', 'careers@rbsn.example.com', 0, 'open', '2026-08-16 09:00:00'),
('Relationship Manager', 'Guaranty Trust Bank', (SELECT id FROM companies WHERE name='Guaranty Trust Bank'), '#e65100', '#ffffff', 'Abuja, Nigeria', 'Full-time', 'Manage and grow a portfolio of corporate banking relationships, delivering tailored financial solutions.', (SELECT about FROM companies WHERE name='Guaranty Trust Bank'), 'email', 'careers@rbsn.example.com', 0, 'open', '2026-08-13 09:00:00');

INSERT INTO job_category (job_id, category_id)
SELECT j.id, c.id FROM jobs j JOIN categories c ON c.type='job' AND (
  (j.title='Senior Business Consultant' AND c.slug='consulting') OR
  (j.title='Strategy & Operations Manager' AND c.slug IN ('consulting','operations')) OR
  (j.title='Relationship Manager' AND c.slug='banking-finance')
);

-- Events
INSERT INTO events (title, description, location, is_virtual, event_date, event_time) VALUES
('Alumni Networking Evening', 'An evening of connection and celebration with fellow RBSN alumni in Lagos.', 'Lagos, Nigeria', 0, '2026-09-05', '6:00 PM WAT'),
('Career Advancement Workshop', 'A virtual workshop covering resume building, interview prep, and career strategy.', 'Virtual Event', 1, '2026-09-18', '10:00 AM WAT'),
('Entrepreneurship Forum', 'Join fellow founders and business leaders for a forum on scaling ventures across Africa.', 'Abuja, Nigeria', 0, '2026-09-28', '11:00 AM WAT');

-- Resources
INSERT INTO resources (title, description, link, category) VALUES
('Resume & CV Templates', 'Professionally designed templates to help you stand out to employers.', '#', 'Career'),
('Interview Preparation Guide', 'A complete guide to acing behavioural and case-style interviews.', '#', 'Career'),
('LinkedIn Optimization Toolkit', 'Tips and templates to strengthen your professional online presence.', '#', 'Career'),
('Entrepreneurship Starter Kit', 'Templates and frameworks for validating and launching a new venture.', '#', 'Business');

-- Homepage hero slides
INSERT INTO hero_slides (sort_order, enabled, image, title, highlight, subtitle, description, content_align, btn1_text, btn1_show, btn2_text, btn2_show) VALUES
(1, 1, 'https://blogger.googleusercontent.com/img/b/R29vZ2xl/AVvXsEiKHGek-SVk8Ls1vUimFtCWj9fGpOmChc_sWwDiwXaJnaHSbD2fCrUSR9Yeaa951Q2QV9JkuUfBG2lBRa0EZbD551u-XKxQ6fm-n0qLRiLg4j2wPhOKHoOckt2vSthfzst3X1kmAxg-uxO6Pyf5kkQC4nZ4gy-jMvx0eEQfcvsr5AoLUzdBTLcO7ppzpe6S/s1600/alumni%20graduates%204.png', 'Welcome to the', 'RBSN Alumni Network', 'Stay connected. Grow together. Make an impact.', 'Join a global community of accomplished leaders and access exclusive opportunities, resources, and connections.', 'left', 'Explore Opportunities', 1, 'Update Your Profile', 1),
(2, 1, 'https://images.unsplash.com/photo-1523240795612-9a054b0db644?auto=format&fit=crop&w=1600&q=80', 'Grow Your', 'Professional Network', 'Thousands of alumni. One community.', 'Reconnect with classmates, meet industry leaders, and expand your circle across 35+ countries.', 'left', 'Explore Opportunities', 1, 'Update Your Profile', 1),
(3, 1, 'https://images.unsplash.com/photo-1521737604893-d14cc237f11d?auto=format&fit=crop&w=1600&q=80', 'Discover Your Next', 'Career Opportunity', 'Curated roles from trusted employers.', 'Browse job openings shared exclusively with RBSN alumni by our partner companies.', 'left', 'Explore Opportunities', 1, 'Update Your Profile', 1),
(4, 1, 'https://images.unsplash.com/photo-1552664730-d307ca884978?auto=format&fit=crop&w=1600&q=80', 'Give Back As A', 'Mentor & Leader', 'Shape the next generation of leaders.', 'Share your experience through mentorship programs designed to help rising alumni succeed.', 'left', 'Explore Opportunities', 1, 'Update Your Profile', 1),
(5, 1, 'https://images.unsplash.com/photo-1540575467063-178a50c2df87?auto=format&fit=crop&w=1600&q=80', 'Celebrate Our', 'Alumni Success Stories', 'Real stories. Real impact.', 'Read how fellow graduates are transforming industries across Africa and beyond.', 'left', 'Explore Opportunities', 1, 'Update Your Profile', 1);

-- Site settings (hero behavior, homepage counts, CTA banner, metrics bar)
INSERT INTO settings (setting_key, setting_value) VALUES
('site_name', 'Rome Business School Nigeria - Alumni Network'),

('hero_height', '480'),
('hero_interval', '6'),
('hero_transition', 'fade'),

('home_news_count', '4'),
('home_jobs_count', '3'),
('home_events_count', '3'),

('cta_subtitle', 'GIVE BACK. MAKE AN IMPACT.'),
('cta_title', 'Mentor the next generation of RBSN leaders.'),
('metric_alumni', '3,500+'),
('metric_countries', '35+'),
('metric_jobs', '1,200+'),
('metric_connections', '2,000+'),
('metric_events', '120+'),
('giving_contact_email', 'giving@rbsn.example.com'),
('save_job_redirects_to_details', '0'),
('job_posting_mode', 'approval'),
('menu_accent_color', '#d49326'),
('header_height', '64'),
('dropdown_offset', '4'),
('metric_scholarships', '128'),
('metric_mentors_engaged', '32'),
('metric_projects_funded', '5'),
('theme_mode', 'unified'),
('theme_accent_color', '#d49326'),
('theme_button_color', '#d49326'),
('theme_map_color', '#d49326');

-- ---------------------------------------------------------------------
-- page_visibility  (per-page audience controlled by admin: public / any
-- logged-in alumni / editor-tagged alumni and up / admin-only —
-- enforced both in nav rendering and in each page's own controller
-- via Controller::requireVisibility())
-- ---------------------------------------------------------------------
CREATE TABLE page_visibility (
  page_key VARCHAR(60) PRIMARY KEY,
  label VARCHAR(100) NOT NULL,
  audience ENUM('public','alumni','editor','admin') NOT NULL DEFAULT 'public',
  show_in_nav TINYINT(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB;

INSERT INTO page_visibility (page_key, label, audience) VALUES
('directory', 'Alumni Directory', 'public'),
('connections', 'Connections', 'alumni'),
('feed', 'Community Feed', 'alumni'),
('messages', 'Messages', 'alumni'),
('mentorship', 'Mentorship', 'public'),
('businesses', 'Alumni Businesses', 'public'),
('jobs', 'Job Board', 'public'),
('companies', 'Companies Hiring', 'public'),
('events', 'Events', 'public'),
('knowledge', 'Knowledge Hub', 'public'),
('resources', 'Career Resources', 'public'),
('benefits', 'Alumni Benefits', 'public'),
('give', 'Give Back', 'public'),
('news', 'News & Blog', 'public'),
('spotlight', 'Alumni Spotlight', 'public'),
('map', 'Alumni Map', 'public'),
('cohorts', 'Cohorts', 'public');

-- ---------------------------------------------------------------------
-- page_tab_visibility  (per-tab visibility within the site's unified
-- tabbed pages — e.g. hide just "Interview Prep" while keeping the rest
-- of Resources visible. page_key 'events_mine' is /events/mine, kept
-- distinct from 'events' since they're different controllers/tab sets.)
-- ---------------------------------------------------------------------
CREATE TABLE page_tab_visibility (
  page_key VARCHAR(60) NOT NULL,
  tab_key VARCHAR(60) NOT NULL,
  label VARCHAR(100) NOT NULL,
  is_visible TINYINT(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (page_key, tab_key)
) ENGINE=InnoDB;

INSERT INTO page_tab_visibility (page_key, tab_key, label) VALUES
('resources', 'career', 'Career Resources'),
('resources', 'resume-review', 'Resume Review'),
('resources', 'interview-prep', 'Interview Prep'),
('jobs', 'board', 'Job Board'),
('jobs', 'search', 'Advanced Search'),
('jobs', 'companies', 'Companies Hiring'),
('jobs', 'saved', 'Saved Jobs'),
('jobs', 'alerts', 'Job Alerts'),
('events', 'all', 'All Events'),
('events', 'global', 'Global Events'),
('events', 'reunions', 'Reunions'),
('events', 'executive', 'Executive Programmes'),
('events', 'webinars', 'Webinars'),
('events', 'featured', 'Featured Events'),
('events_mine', 'registrations', 'My Registrations'),
('events_mine', 'saved', 'Saved Events'),
('events_mine', 'history', 'Event History');

-- ---------------------------------------------------------------------
-- link_visibility  (per-link visibility within the nav menus — for cases
-- where several distinct links point at the same page/tab, e.g. "Create
-- a Poll" and "Start a Discussion" both go to /feed. link_key is derived
-- automatically at render time as "{page_key|other}:{slug(title)}" by
-- link_visibility_key() in app/Core/helpers.php, so new mega_item()/mobile
-- links need no code change here -- they simply default to visible until
-- a matching row is added. Rows not present default to visible (fail-open).)
-- ---------------------------------------------------------------------
CREATE TABLE link_visibility (
  link_key VARCHAR(191) PRIMARY KEY,
  page_key VARCHAR(60) NOT NULL,
  label VARCHAR(160) NOT NULL,
  is_visible TINYINT(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB;

INSERT INTO link_visibility (link_key, page_key, label, is_visible) VALUES
('benefits:all-benefits', 'benefits', 'All Benefits', 1),
('benefits:continuing-education', 'benefits', 'Continuing Education', 1),
('benefits:discounts-offers', 'benefits', 'Discounts & Offers', 1),
('benefits:executive-education', 'benefits', 'Executive Education', 1),
('benefits:library-resources', 'benefits', 'Library & Resources', 1),
('benefits:professional-services', 'benefits', 'Professional Services', 1),
('benefits:travel-lifestyle', 'benefits', 'Travel & Lifestyle', 1),
('businesses:alumni-businesses', 'businesses', 'Alumni Businesses', 1),
('businesses:business-referrals', 'businesses', 'Business Referrals', 1),
('connections:connection-requests', 'connections', 'Connection Requests', 1),
('connections:following', 'connections', 'Following', 1),
('connections:my-connections', 'connections', 'My Connections', 1),
('connections:suggested-connections', 'connections', 'Suggested Connections', 1),
('directory:alumni-directory-access', 'directory', 'Alumni Directory Access', 1),
('directory:alumni-map', 'directory', 'Alumni Map', 1),
('directory:find-by-cohort', 'directory', 'Find by Cohort', 1),
('directory:saved-alumni', 'directory', 'Saved Alumni', 1),
('directory:search-alumni', 'directory', 'Search Alumni', 1),
('directory:talent-search', 'directory', 'Talent Search', 1),
('events:all-events', 'events', 'All Events', 1),
('events:career-webinars', 'events', 'Career Webinars', 1),
('events:case-competitions', 'events', 'Case Competitions', 1),
('events:executive-programmes', 'events', 'Executive Programmes', 1),
('events:featured-events', 'events', 'Featured Events', 1),
('events:global-events', 'events', 'Global Events', 1),
('events:networking-events', 'events', 'Networking Events', 1),
('events:reunions', 'events', 'Reunions', 1),
('events:webinars', 'events', 'Webinars', 1),
('events:webinars-workshops', 'events', 'Webinars & Workshops', 1),
('events_mine:event-history', 'events_mine', 'Event History', 1),
('events_mine:my-events', 'events_mine', 'My Events', 1),
('events_mine:my-registrations', 'events_mine', 'My Registrations', 1),
('events_mine:my-tickets', 'events_mine', 'My Tickets', 1),
('events_mine:saved-events', 'events_mine', 'Saved Events', 1),
('feed:community-feed', 'feed', 'Community Feed', 1),
('feed:create-a-poll', 'feed', 'Create a Poll', 1),
('feed:start-a-discussion', 'feed', 'Start a Discussion', 1),
('give:give-back', 'give', 'Give Back', 1),
('give:support-alumni-events', 'give', 'Support Alumni Events', 1),
('jobs:advanced-search', 'jobs', 'Advanced Search', 1),
('jobs:companies-hiring', 'jobs', 'Companies Hiring', 1),
('jobs:internship-opportunities', 'jobs', 'Internship Opportunities', 1),
('jobs:job-alerts', 'jobs', 'Job Alerts', 1),
('jobs:job-board', 'jobs', 'Job Board', 1),
('jobs:my-job-posts', 'jobs', 'My Job Posts', 1),
('jobs:post-a-job', 'jobs', 'Post a Job', 1),
('jobs:saved-jobs', 'jobs', 'Saved Jobs', 1),
('knowledge:all-articles', 'knowledge', 'All Articles', 1),
('knowledge:case-studies', 'knowledge', 'Case Studies', 1),
('knowledge:executive-summaries', 'knowledge', 'Executive Summaries', 1),
('knowledge:featured-contributors', 'knowledge', 'Featured Contributors', 1),
('knowledge:knowledge-hub', 'knowledge', 'Knowledge Hub', 1),
('knowledge:my-collections', 'knowledge', 'My Collections', 1),
('knowledge:recently-added', 'knowledge', 'Recently Added', 1),
('knowledge:recommended-for-you', 'knowledge', 'Recommended for You', 1),
('knowledge:research-publications', 'knowledge', 'Research & Publications', 1),
('knowledge:speaking-opportunities', 'knowledge', 'Speaking Opportunities', 1),
('knowledge:submit-research', 'knowledge', 'Submit Research', 1),
('knowledge:thought-leadership', 'knowledge', 'Thought Leadership', 1),
('knowledge:topics-categories', 'knowledge', 'Topics & Categories', 1),
('knowledge:trending-now', 'knowledge', 'Trending Now', 1),
('knowledge:videos-podcasts', 'knowledge', 'Videos & Podcasts', 1),
('knowledge:write-an-article', 'knowledge', 'Write an Article', 1),
('map:global-alumni-access', 'map', 'Global Alumni Access', 1),
('mentorship:become-a-mentor', 'mentorship', 'Become a Mentor', 1),
('mentorship:mentorship', 'mentorship', 'Mentorship', 1),
('mentorship:mentorship-programs', 'mentorship', 'Mentorship Programs', 1),
('messages:messages', 'messages', 'Messages', 1),
('news:news-blog', 'news', 'News & Blog', 1),
('other:admin-panel', 'other', 'Admin Panel', 1),
('other:applications', 'other', 'Applications', 1),
('other:attendee-list', 'other', 'Attendee List', 1),
('other:create-event', 'other', 'Create Event', 1),
('other:event-reports', 'other', 'Event Reports', 1),
('other:manage-events', 'other', 'Manage Events', 1),
('other:manage-jobs', 'other', 'Manage Jobs', 1),
('other:my-alerts', 'other', 'My Alerts', 1),
('other:notifications', 'other', 'Notifications', 1),
('other:post-a-job', 'other', 'Post a Job', 1),
('other:promote-event', 'other', 'Promote Event', 1),
('other:submit-research', 'other', 'Submit Research', 1),
('other:write-an-article', 'other', 'Write an Article', 1),
('resources:career-development', 'resources', 'Career Development', 1),
('resources:career-resources', 'resources', 'Career Resources', 1),
('resources:interview-prep', 'resources', 'Interview Prep', 1),
('resources:resource-library', 'resources', 'Resource Library', 1),
('resources:resume-review', 'resources', 'Resume Review', 1),
('spotlight:alumni-spotlight', 'spotlight', 'Alumni Spotlight', 1);

-- ---------------------------------------------------------------------
-- giving_causes  (individual "ways to give" pages linked from the Give
-- Back mega-menu — each has its own detail page at /give/causes/{slug})
-- ---------------------------------------------------------------------
CREATE TABLE giving_causes (
  id INT AUTO_INCREMENT PRIMARY KEY,
  column_group ENUM('support','involve','impact') NOT NULL DEFAULT 'support',
  slug VARCHAR(120) NOT NULL UNIQUE,
  title VARCHAR(150) NOT NULL,
  description VARCHAR(255) NULL,
  body MEDIUMTEXT NULL,
  icon VARCHAR(60) NOT NULL DEFAULT 'fa-solid fa-heart',
  sort_order INT NOT NULL DEFAULT 0,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

INSERT INTO giving_causes (column_group, slug, title, description, body, icon, sort_order) VALUES
('support','make-a-donation','Make a Donation','Contribute to initiatives that transform lives.','<p>Every gift, no matter the size, helps RBSN expand access to world-class business education. Unrestricted donations are directed to the areas of greatest need each academic year &mdash; from financial aid to campus resources.</p>','fa-solid fa-heart',1),
('support','scholarships','Scholarships','Support students through scholarships and bursaries.','<p>Scholarships open doors for talented students who could not otherwise afford an RBSN education. Your contribution can fund a full or partial scholarship and be named in honour of someone who inspired you.</p>','fa-solid fa-graduation-cap',2),
('support','academic-excellence','Academic Excellence','Fund faculty, research and academic programs.','<p>Support the recruitment of world-class faculty, curriculum innovation, and academic programming that keeps RBSN at the forefront of business education in Africa.</p>','fa-solid fa-building-columns',3),
('support','innovation-research','Innovation & Research','Drive innovation and research that matters.','<p>Fund faculty and student research addressing real business challenges across African markets, from fintech adoption to sustainable supply chains.</p>','fa-regular fa-lightbulb',4),
('support','student-development','Student Development','Empower student clubs, projects and competitions.','<p>Support student-led clubs, case competitions, and leadership development programs that build well-rounded, career-ready graduates.</p>','fa-solid fa-seedling',5),
('support','facilities-infrastructure','Facilities & Infrastructure','Help build world-class facilities.','<p>Contribute to campus facilities &mdash; classrooms, technology, and shared spaces &mdash; that shape the learning experience for generations of RBSN students.</p>','fa-solid fa-trophy',6),
('involve','volunteer','Volunteer','Share your time and expertise.','<p>Volunteer your time for career panels, admissions events, orientation programs, or alumni chapter activities. Every hour you give strengthens the RBSN community.</p>','fa-regular fa-hand',1),
('involve','guest-speaker','Guest Speaker','Share your insights and experiences.','<p>Guest speakers bring real-world perspective into the classroom. Share your career journey, industry expertise, or entrepreneurial story with current students.</p>','fa-solid fa-microphone',2),
('involve','industry-connect','Industry Connect','Create opportunities for students.','<p>Help connect RBSN students and graduates with opportunities at your organization &mdash; from site visits and case studies to hiring pipelines.</p>','fa-solid fa-handshake',3),
('involve','pro-bono-support','Pro Bono Support','Provide professional expertise for good.','<p>Offer pro bono consulting, legal, financial, or strategic support to student ventures and RBSN-affiliated social impact projects.</p>','fa-solid fa-scale-balanced',4),
('impact','start-a-fund','Start a Fund','Establish a fund for a cause you care about.','<p>Work with the Giving team to establish a dedicated fund &mdash; for scholarships, research, or a cause close to your heart &mdash; with terms you help define.</p>','fa-solid fa-hand-holding-dollar',1),
('impact','corporate-partnerships','Corporate Partnerships','Partner with the school to drive impact.','<p>Partner your organization with RBSN through sponsorships, joint research, recruiting partnerships, or co-branded executive education programs.</p>','fa-solid fa-building',2),
('impact','endow-a-chair','Endow a Chair','Support academic chairs and professorships.','<p>Endowing a faculty chair is one of the most enduring ways to shape RBSN''s academic future, attracting and retaining top scholars in a chosen field.</p>','fa-solid fa-chair',3),
('impact','leave-a-legacy','Leave a Legacy','Include the Business School in your estate plans.','<p>Planned gifts &mdash; bequests, trusts, and other estate commitments &mdash; ensure your impact on RBSN continues for generations to come.</p>','fa-solid fa-leaf',4),
('impact','giving-societies','Giving Societies','Join leadership giving societies.','<p>Join fellow alumni donors in RBSN''s leadership giving societies, recognizing sustained and significant contributions to the school''s mission.</p>','fa-solid fa-people-group',5),
('impact','impact-reports','Impact Reports','See how your support makes a difference.','<p>Explore how alumni giving has translated into scholarships awarded, research funded, and student lives changed over the past year.</p>','fa-solid fa-chart-column',6);

-- ---------------------------------------------------------------------
-- campaigns  (giving campaigns shown on the Give Back page)
-- ---------------------------------------------------------------------
CREATE TABLE campaigns (
  id INT AUTO_INCREMENT PRIMARY KEY,
  title VARCHAR(200) NOT NULL,
  description TEXT NULL,
  image VARCHAR(255) NULL,
  goal_amount DECIMAL(12,2) NOT NULL DEFAULT 0,
  raised_amount DECIMAL(12,2) NOT NULL DEFAULT 0,
  status ENUM('active','closed') NOT NULL DEFAULT 'active',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- donation_methods  (admin-managed bank details revealed when an
-- alumnus wants to give to any campaign — one global list)
-- ---------------------------------------------------------------------
CREATE TABLE donation_methods (
  id INT AUTO_INCREMENT PRIMARY KEY,
  label VARCHAR(150) NOT NULL,
  bank_name VARCHAR(150) NULL,
  account_name VARCHAR(150) NULL,
  account_number VARCHAR(50) NULL,
  sort_code VARCHAR(20) NULL,
  sort_order INT NOT NULL DEFAULT 0,
  is_active TINYINT(1) NOT NULL DEFAULT 1,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- donation_confirmations  (self-reported "I gave" records an alumnus
-- submits after transferring funds; admin reconciles raised_amount
-- against these manually — no payment processing happens here)
-- ---------------------------------------------------------------------
CREATE TABLE donation_confirmations (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  campaign_id INT NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_donation_confirmations_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
  CONSTRAINT fk_donation_confirmations_campaign FOREIGN KEY (campaign_id) REFERENCES campaigns(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- saved_jobs / saved_alumni / saved_news_posts  (per-user bookmarks)
-- ---------------------------------------------------------------------
CREATE TABLE saved_jobs (
  user_id INT NOT NULL,
  job_id INT NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (user_id, job_id),
  CONSTRAINT fk_saved_jobs_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
  CONSTRAINT fk_saved_jobs_job FOREIGN KEY (job_id) REFERENCES jobs(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE saved_news_posts (
  user_id INT NOT NULL,
  news_post_id INT NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (user_id, news_post_id),
  CONSTRAINT fk_saved_news_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
  CONSTRAINT fk_saved_news_post FOREIGN KEY (news_post_id) REFERENCES news_posts(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- job_applications  (click-through "Apply Now" tracking, powers the Saved Jobs "Applied" tab)
-- ---------------------------------------------------------------------
CREATE TABLE job_applications (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  job_id INT NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY uniq_user_job (user_id, job_id),
  CONSTRAINT fk_job_applications_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
  CONSTRAINT fk_job_applications_job FOREIGN KEY (job_id) REFERENCES jobs(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- job_alerts  (saved search criteria; matches are computed live on page view, no email delivery)
-- ---------------------------------------------------------------------
CREATE TABLE job_alerts (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  name VARCHAR(150) NOT NULL,
  keywords VARCHAR(150) NULL,
  location VARCHAR(150) NULL,
  category_id INT NULL,
  job_type VARCHAR(50) NULL,
  work_mode ENUM('onsite','remote','hybrid') NULL,
  frequency ENUM('daily','weekly') NOT NULL DEFAULT 'daily',
  is_active TINYINT(1) NOT NULL DEFAULT 1,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  CONSTRAINT fk_job_alerts_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
  CONSTRAINT fk_job_alerts_category FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL
) ENGINE=InnoDB;

CREATE TABLE saved_alumni (
  user_id INT NOT NULL,
  alumni_id INT NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (user_id, alumni_id),
  CONSTRAINT fk_saved_alumni_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
  CONSTRAINT fk_saved_alumni_alumni FOREIGN KEY (alumni_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- businesses  (alumni-owned business directory) + saved_businesses
-- ---------------------------------------------------------------------
CREATE TABLE businesses (
  id INT NOT NULL AUTO_INCREMENT,
  owner_id INT NOT NULL,
  name VARCHAR(150) NOT NULL,
  category VARCHAR(100) DEFAULT NULL,
  business_type VARCHAR(100) DEFAULT NULL,
  description TEXT DEFAULT NULL,
  logo VARCHAR(255) DEFAULT NULL,
  website VARCHAR(255) DEFAULT NULL,
  location VARCHAR(150) DEFAULT NULL,
  founded_year SMALLINT DEFAULT NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  KEY owner_id (owner_id),
  CONSTRAINT businesses_ibfk_1 FOREIGN KEY (owner_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE saved_businesses (
  user_id INT NOT NULL,
  business_id INT NOT NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (user_id, business_id),
  KEY business_id (business_id),
  CONSTRAINT saved_businesses_ibfk_1 FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
  CONSTRAINT saved_businesses_ibfk_2 FOREIGN KEY (business_id) REFERENCES businesses(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- blocked_users  (blocking another alumnus prevents future connection requests)
-- ---------------------------------------------------------------------
CREATE TABLE blocked_users (
  blocker_id INT NOT NULL,
  blocked_id INT NOT NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (blocker_id, blocked_id),
  KEY blocked_id (blocked_id),
  CONSTRAINT blocked_users_ibfk_1 FOREIGN KEY (blocker_id) REFERENCES users(id) ON DELETE CASCADE,
  CONSTRAINT blocked_users_ibfk_2 FOREIGN KEY (blocked_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- mentorship_requests  (was missing from this file — reverse-engineered from the live DB)
-- ---------------------------------------------------------------------
CREATE TABLE mentorship_requests (
  id INT NOT NULL AUTO_INCREMENT,
  mentor_id INT NOT NULL,
  requester_id INT NOT NULL,
  area VARCHAR(150) DEFAULT NULL,
  message TEXT DEFAULT NULL,
  status ENUM('pending','accepted','declined') NOT NULL DEFAULT 'pending',
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  responded_at TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (id),
  KEY mentor_id (mentor_id),
  KEY requester_id (requester_id),
  CONSTRAINT mentorship_requests_ibfk_1 FOREIGN KEY (mentor_id) REFERENCES users(id) ON DELETE CASCADE,
  CONSTRAINT mentorship_requests_ibfk_2 FOREIGN KEY (requester_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- notifications  (was missing from this file — reverse-engineered from the live DB)
-- ---------------------------------------------------------------------
CREATE TABLE notifications (
  id INT NOT NULL AUTO_INCREMENT,
  user_id INT NOT NULL,
  type VARCHAR(50) NOT NULL,
  message VARCHAR(255) NOT NULL,
  link VARCHAR(255) DEFAULT NULL,
  is_read TINYINT(1) NOT NULL DEFAULT 0,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  KEY user_id (user_id),
  CONSTRAINT notifications_ibfk_1 FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;
