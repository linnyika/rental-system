package com.rentalmanagement.app.models;

import com.google.gson.annotations.SerializedName;

import java.util.List;

public class TasksResponse {

    @SerializedName("tasks")
    private List<TaskItem> tasks;

    public List<TaskItem> getTasks() {
        return tasks;
    }
}
