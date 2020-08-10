package main

import (
	"../../internal/repository/db"
	"github.com/gorilla/mux"
	"log"
	"net/http"
)

func main() {
	// Connect with Database
	err := db.Open()
	if err != nil {
		log.Fatal(err.Error())
	}

	// Initialize Server Requests
	router := mux.NewRouter()
	//router.HandleFunc("/", controller.GetAllMessages).Methods("GET")

	// Open Server
	err = http.ListenAndServe(":80", router)
	if err != nil {
		log.Fatal(err.Error())
	}
}