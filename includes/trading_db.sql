CREATE TABLE Users (
    user_id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL
);

CREATE TABLE Transactions (
    transaction_id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT,
    ticker VARCHAR(10),
    price DECIMAL(10, 2),
    quantity INT,
    type ENUM('buy', 'sell'),
    timestamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES Users(user_id)
);

CREATE TABLE Portfolio (
    user_id INT,
    ticker VARCHAR(10),
    average_price DECIMAL(10, 2),
    quantity INT,
    PRIMARY KEY (user_id, ticker),
    FOREIGN KEY (user_id) REFERENCES Users(user_id)
);