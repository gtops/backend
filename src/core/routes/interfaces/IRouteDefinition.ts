export interface IRouteDefinition {
	method?: EMethod;
	path: string;
	handler: string;
	controller: string;
}

export enum EMethod {
	GET = "GET",
	POST = "POST",
	PUT = "PUT",
	DELETE = "DELETE"
}
