import { Schema } from "joi";

export interface IRouteDefinition {
	method?: EMethod;
	path: string;
	handler: string;
	controller: string;
	validate?: Schema;
}

export enum EMethod {
	GET = "GET",
	POST = "POST",
	PUT = "PUT",
	DELETE = "DELETE"
}
