package main

import (
	"../../internal/controller"
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

	// Define "assets" folder as public
	http.Handle("/assets/", http.StripPrefix("/assets/", http.FileServer(http.Dir("./assets"))))

	// Initialize Server Requests
	router := mux.NewRouter()
	router.HandleFunc("/", controller.GetHomePage).Methods("GET")

	// Open Server
	err = http.ListenAndServe(":80", router)
	if err != nil {
		log.Fatal(err.Error())
	}
}