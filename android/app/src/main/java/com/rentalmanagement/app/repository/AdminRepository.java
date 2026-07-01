package com.rentalmanagement.app.repository;

import com.rentalmanagement.app.models.AdminLandlordsResponse;
import com.rentalmanagement.app.models.ApiMessageResponse;
import com.rentalmanagement.app.models.UpdateLandlordRequest;

import retrofit2.Call;

public class AdminRepository extends BaseRepository {

    public AdminRepository() {
        super();
    }

    public Call<AdminLandlordsResponse> landlords(String authorization) {
        return apiService.adminLandlords(authorization);
    }

    public Call<ApiMessageResponse> updateLandlord(String authorization, long landlordId, UpdateLandlordRequest request) {
        return apiService.updateLandlord(authorization, landlordId, request);
    }

    public Call<ApiMessageResponse> deleteLandlord(String authorization, long landlordId) {
        return apiService.deleteLandlord(authorization, landlordId);
    }
}
