package db

import (
	"database/sql"
	_ "github.com/go-sql-driver/mysql"
)

var Database *sql.DB

func Open() error {
	db, err := sql.Open("mysql", "root:admin@(localhost:3306)/MovieGroup?parseTime=true")
	if err != nil {
		return err
	}

	err = db.Ping()
	if err != nil {
		return err
	}

	Database = db
	return nil
}