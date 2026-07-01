package com.rentalmanagement.app.repository;

import com.rentalmanagement.app.models.AddPropertyRequest;
import com.rentalmanagement.app.models.ApiMessageResponse;
import com.rentalmanagement.app.models.PropertiesResponse;

import retrofit2.Call;

public class PropertyRepository extends BaseRepository {

    public PropertyRepository() {
        super();
    }

    public Call<PropertiesResponse> properties(String authorization) {
        return apiService.properties(authorization);
    }

    public Call<ApiMessageResponse> addProperty(String authorization, AddPropertyRequest request) {
        return apiService.addProperty(authorization, request);
    }
}
