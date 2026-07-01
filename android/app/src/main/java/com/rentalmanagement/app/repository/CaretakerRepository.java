package com.rentalmanagement.app.repository;

import com.rentalmanagement.app.models.ApiMessageResponse;
import com.rentalmanagement.app.models.RegisterCaretakerRequest;

import retrofit2.Call;

public class CaretakerRepository extends BaseRepository {

    public CaretakerRepository() {
        super();
    }

    public Call<ApiMessageResponse> registerCaretaker(String authorization, RegisterCaretakerRequest request) {
        return apiService.registerCaretaker(authorization, request);
    }
}
