USE login;

CREATE TABLE news (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    description TEXT NOT NULL,
    link VARCHAR(255) NOT NULL,
    pub_date DATETIME NOT NULL,
    source VARCHAR(255) NOT NULL
);
