package com.rentalmanagement.app.repository;

import com.rentalmanagement.app.api.ApiClient;
import com.rentalmanagement.app.api.ApiService;

public abstract class BaseRepository {

    protected final ApiService apiService;

    protected BaseRepository() {
        this.apiService = ApiClient.getApiService();
    }
}
