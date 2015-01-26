// GENERATED CODE - DO NOT EDIT
package routes

import "github.com/revel/revel"


type tGorpController struct {}
var GorpController tGorpController


func (_ tGorpController) Begin(
		) string {
	args := make(map[string]string)
	
	return revel.MainRouter.Reverse("GorpController.Begin", args).Url
}

func (_ tGorpController) Commit(
		) string {
	args := make(map[string]string)
	
	return revel.MainRouter.Reverse("GorpController.Commit", args).Url
}

func (_ tGorpController) Rollback(
		) string {
	args := make(map[string]string)
	
	return revel.MainRouter.Reverse("GorpController.Rollback", args).Url
}


type tLogonCtrl struct {}
var LogonCtrl tLogonCtrl


func (_ tLogonCtrl) HasUsers(
		) string {
	args := make(map[string]string)
	
	return revel.MainRouter.Reverse("LogonCtrl.HasUsers", args).Url
}


