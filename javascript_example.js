function authenticate($q, UserService, $state, $transitions, $location, $rootScope) {
    var deferred = $q.defer();
    if (UserService.isAuth()) {
        deferred.resolve();
        var fromState = $state;
        var globalmenu = ["page.login", "pengguna.profil", "app.main", "page.500", "app.generator"];
        $transitions.onStart({}, function ($transition$) {
            var toState = $transition$.$to();
            if ($rootScope.user.akses[toState.name.replace(".", "_")] || globalmenu.indexOf(toState.name)) { } else {
                $state.target("page.500")
            }
        });
    } else {
        $location.path("/login");
    }
    return deferred.promise;
}


$scope.save = function (form) {
    Data.post("appsetting/save", form).then(function (result) {
        if (result.status_code == 200) {
            $rootScope.alert("Berhasil", "Data berhasil disimpan", "Success");
            $scope.cancel();
        } else {
            $rootScope.alert("Terjadi Kesalahan", setErrorMessage(result.errors), "Error");
        }
        $scope.loading = false;
    });
};


