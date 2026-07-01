package com.rentalmanagement.app.repository;

import com.rentalmanagement.app.models.ApiMessageResponse;
import com.rentalmanagement.app.models.RegisterTenantRequest;

import retrofit2.Call;

public class TenantRepository extends BaseRepository {

    public TenantRepository() {
        super();
    }

    public Call<ApiMessageResponse> registerTenant(String authorization, RegisterTenantRequest request) {
        return apiService.registerTenant(authorization, request);
    }
}
