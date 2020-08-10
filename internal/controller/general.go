package controller

import (
	"../resource"
	"html/template"
	"log"
	"net/http"
	"os"
	"path/filepath"
	"strconv"
	"strings"
)

/*
	General Template Entities
*/
type Page struct {
	Title string
	Data interface{}
}

type Error struct {
	Status int
	Description string
}

/*
	General Controller Methods
*/
func RenderTemplate(w http.ResponseWriter, tmpl string, data interface{}) {
	cwd, _ := os.Getwd()

	t, err := template.ParseFiles(filepath.Join(cwd, "web/templates/" + tmpl + ".html"))
	if err != nil {
		GetErrorPage(w, http.StatusInternalServerError)
		log.Println(err)
		return
	}

	err = t.Execute(w, data)
	if err != nil {
		GetErrorPage(w, http.StatusInternalServerError)
		log.Println(err)
	}
}

func GetErrorPage(w http.ResponseWriter, status int) {
	data := Page {
		Title: "Erro - " + strconv.Itoa(status),
		Data: Error{
			Status: status,
			Description: "Error interno no servidor.",
		},
	}

	tmpl := template.Must(template.ParseFiles("web/templates/error.html"))
	tmpl.Execute(w, data)
}

func FindRequestLanguage(r *http.Request) string {
	langs := r.Header["Accept-Language"]
	if len(langs) == 0 {
		return "en"
	}

	for key := range resource.Messages {
		for _, lang := range langs {
			if strings.Contains(strings.ToLower(lang), strings.ToLower(key)) {
				return key
			}
		}
	}

	return "en"
}

/*
	General Requests Handlers
*/
// GET("/")
func GetHomePage(w http.ResponseWriter, r *http.Request) {
	lang := FindRequestLanguage(r)

	data := Page {
		Title: resource.Messages[lang]["app.title"],
	}
	RenderTemplate(w, "home", data)
}