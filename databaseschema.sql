-- Food Capstone Project Database Schema
-- Team 3 - Capstone 2025

CREATE TABLE foods (
    id INT AUTO_INCREMENT PRIMARY KEY,
    food_name VARCHAR(255) NOT NULL,
    brand_name VARCHAR(255),
    nf_calories DECIMAL(10,2),
    nf_protein DECIMAL(10,2),
    nf_total_carbohydrate DECIMAL(10,2),
    nf_total_fat DECIMAL(10,2),
    serving_qty DECIMAL(10,2),
    serving_unit VARCHAR(50),
    consumed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE ingredients (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE meals (
    meal_id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    category VARCHAR(100),
    area VARCHAR(100),
    thumbnail TEXT,
    instructions TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE meal_ingredients (
    id INT AUTO_INCREMENT PRIMARY KEY,
    meal_id INT,
    ingredient_id INT,
    measure VARCHAR(100),
    FOREIGN KEY (meal_id) REFERENCES meals(meal_id),
    FOREIGN KEY (ingredient_id) REFERENCES ingredients(id)
);

-- FDC Foods and Nutrients table (from USDA database)
CREATE TABLE fdc_foodsandnutrients (
    fdc_id INT,
    description TEXT,
    data_type VARCHAR(100),
    publication_date DATE,
    food_code INT,
    nutrient_number INT,
    nutrient_name VARCHAR(255),
    amount DECIMAL(15,6),
    unit_name VARCHAR(50),
    derivation_code VARCHAR(10),
    derivation_description TEXT
);