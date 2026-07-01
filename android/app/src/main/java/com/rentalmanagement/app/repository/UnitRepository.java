package com.rentalmanagement.app.repository;

import com.rentalmanagement.app.models.ApiMessageResponse;
import com.rentalmanagement.app.models.UnitCreateRequest;
import com.rentalmanagement.app.models.UnitsResponse;

import retrofit2.Call;

public class UnitRepository extends BaseRepository {

    public UnitRepository() {
        super();
    }

    public Call<UnitsResponse> units(String authorization, int propertyId) {
        return apiService.units(authorization, propertyId);
    }

    public Call<ApiMessageResponse> addUnit(String authorization, int propertyId, UnitCreateRequest request) {
        return apiService.addUnit(authorization, propertyId, request);
    }

    public Call<UnitsResponse> availableUnits(String authorization) {
        return apiService.availableUnits(authorization);
    }
}
