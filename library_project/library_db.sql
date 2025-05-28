-- Table: authors
CREATE TABLE authors (
  author_id int(11) NOT NULL AUTO_INCREMENT,
  author_name varchar(100) NOT NULL,
  country_of_origin varchar(50) DEFAULT NULL,
  PRIMARY KEY (author_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table: publishers
CREATE TABLE publishers (
  publisher_id int(11) NOT NULL AUTO_INCREMENT,
  publisher_name varchar(100) NOT NULL,
  publisher_address text DEFAULT NULL,
  PRIMARY KEY (publisher_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table: books
CREATE TABLE books (
  book_id int(11) NOT NULL AUTO_INCREMENT,
  book_title varchar(255) NOT NULL,
  author_id int(11) NOT NULL,
  publisher_id int(11) NOT NULL,
  publication_year year(4) DEFAULT NULL,
  isbn varchar(20) DEFAULT NULL UNIQUE,
  page_count int(11) DEFAULT NULL,
  synopsis text DEFAULT NULL,
  PRIMARY KEY (book_id),
  KEY fk_book_author (author_id),
  KEY fk_book_publisher (publisher_id),
  CONSTRAINT fk_book_publisher FOREIGN KEY (publisher_id) REFERENCES publishers (publisher_id) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT fk_book_author FOREIGN KEY (author_id) REFERENCES authors (author_id) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;