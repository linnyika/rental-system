package com.rentalmanagement.app.repository;

import com.rentalmanagement.app.models.ApiMessageResponse;
import com.rentalmanagement.app.models.AuthResponse;
import com.rentalmanagement.app.models.CurrentUserResponse;
import com.rentalmanagement.app.models.LoginRequest;
import com.rentalmanagement.app.models.RegisterLandlordRequest;

import retrofit2.Call;

public class AuthRepository extends BaseRepository {

    public AuthRepository() {
        super();
    }

    public Call<AuthResponse> login(LoginRequest request) {
        return apiService.login(request);
    }

    public Call<AuthResponse> landlordSignup(RegisterLandlordRequest request) {
        return apiService.landlordSignup(request);
    }

    public Call<CurrentUserResponse> currentUser(String authorization) {
        return apiService.currentUser(authorization);
    }

    public Call<ApiMessageResponse> logout(String authorization) {
        return apiService.logout(authorization);
    }
}
